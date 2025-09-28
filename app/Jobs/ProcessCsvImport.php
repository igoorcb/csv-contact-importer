<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\ImportSummary;
use App\Models\Contact;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProcessCsvImport implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $importSummaryId,
        public string $filePath
    ) {}

    public function handle(): void
    {
        $importSummary = ImportSummary::find($this->importSummaryId);

        if (!$importSummary) {
            Log::error('ImportSummary not found', ['id' => $this->importSummaryId]);
            return;
        }

        try {
            $importSummary->update(['status' => ImportSummary::STATUS_PROCESSING]);

            $this->processFile($importSummary);

            $importSummary->update(['status' => ImportSummary::STATUS_COMPLETED]);

            Storage::delete($this->filePath);

        } catch (\Exception $e) {
            Log::error('CSV processing failed', [
                'import_id' => $this->importSummaryId,
                'error' => $e->getMessage()
            ]);

            $importSummary->update([
                'status' => ImportSummary::STATUS_FAILED,
                'error_details' => $e->getMessage()
            ]);
        }
    }

    private function processFile(ImportSummary $importSummary): void
    {
        $filePath = Storage::path($this->filePath);
        $handle = fopen($filePath, 'r');

        $separator = $this->detectSeparator($handle);
        rewind($handle);

        $header = fgetcsv($handle, 0, $separator);
        $columnMap = $this->mapColumns($header);

        $totalRows = 0;
        $importedCount = 0;
        $duplicateCount = 0;
        $errorCount = 0;

        while (($row = fgetcsv($handle, 0, $separator)) !== false) {
            $totalRows++;

            try {
                $contactData = $this->mapRowData($row, $columnMap);

                if (!$this->isValidContactData($contactData)) {
                    $errorCount++;
                    continue;
                }

                $existingContact = Contact::where('email', $contactData['email'])->first();

                if ($existingContact) {
                    $duplicateCount++;
                    continue;
                }

                Contact::create($contactData);
                $importedCount++;

            } catch (\Exception $e) {
                $errorCount++;
                Log::warning('Error processing row', [
                    'row' => $totalRows,
                    'data' => $row,
                    'error' => $e->getMessage()
                ]);
            }

            if ($totalRows % 100 === 0) {
                $importSummary->update([
                    'total_rows' => $totalRows,
                    'imported_count' => $importedCount,
                    'duplicate_count' => $duplicateCount,
                    'error_count' => $errorCount,
                ]);
            }
        }

        fclose($handle);

        $importSummary->update([
            'total_rows' => $totalRows,
            'imported_count' => $importedCount,
            'duplicate_count' => $duplicateCount,
            'error_count' => $errorCount,
        ]);
    }

    private function mapColumns(array $header): array
    {
        $normalizedHeader = array_map('strtolower', array_map('trim', $header));

        $columnMap = [];

        foreach ($normalizedHeader as $index => $column) {
            switch ($column) {
                case 'name':
                case 'full_name':
                case 'contact_name':
                    $columnMap['name'] = $index;
                    break;
                case 'email':
                case 'email_address':
                case 'e-mail':
                    $columnMap['email'] = $index;
                    break;
                case 'phone':
                case 'phone_number':
                case 'telephone':
                case 'mobile':
                    $columnMap['phone'] = $index;
                    break;
                case 'birthdate':
                case 'birth_date':
                case 'date_of_birth':
                case 'dob':
                    $columnMap['birthdate'] = $index;
                    break;
            }
        }

        return $columnMap;
    }

    private function mapRowData(array $row, array $columnMap): array
    {
        $data = [];

        if (isset($columnMap['name'])) {
            $data['name'] = trim($row[$columnMap['name']] ?? '');
        }

        if (isset($columnMap['email'])) {
            $data['email'] = strtolower(trim($row[$columnMap['email']] ?? ''));
        }

        if (isset($columnMap['phone'])) {
            $data['phone'] = trim($row[$columnMap['phone']] ?? '') ?: null;
        }

        if (isset($columnMap['birthdate'])) {
            $birthdateStr = trim($row[$columnMap['birthdate']] ?? '');
            $data['birthdate'] = $this->parseBirthdate($birthdateStr);
        }

        return $data;
    }

    private function isValidContactData(array $data): bool
    {
        return !empty($data['name']) &&
               !empty($data['email']) &&
               filter_var($data['email'], FILTER_VALIDATE_EMAIL);
    }

    private function parseBirthdate(string $date): ?string
    {
        if (empty($date)) {
            return null;
        }

        $formats = ['Y-m-d', 'd/m/Y', 'm/d/Y', 'd-m-Y', 'm-d-Y', 'Y/m/d'];

        foreach ($formats as $format) {
            try {
                $parsed = Carbon::createFromFormat($format, $date);
                if ($parsed && $parsed->year >= 1900 && $parsed->year <= now()->year) {
                    return $parsed->format('Y-m-d');
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return null;
    }

    private function detectSeparator($handle): string
    {
        $separators = [',', ';', "\t"];
        $bestSeparator = ',';
        $maxColumns = 0;

        $position = ftell($handle);

        foreach ($separators as $separator) {
            rewind($handle);
            $testRow = fgetcsv($handle, 0, $separator);

            if ($testRow && count($testRow) > $maxColumns) {
                $maxColumns = count($testRow);
                $bestSeparator = $separator;
            }
        }

        fseek($handle, $position);
        return $bestSeparator;
    }

    public function failed(\Throwable $exception): void
    {
        $importSummary = ImportSummary::find($this->importSummaryId);

        if ($importSummary) {
            $importSummary->update([
                'status' => ImportSummary::STATUS_FAILED,
                'error_details' => $exception->getMessage()
            ]);
        }

        Log::error('CSV processing job failed', [
            'import_id' => $this->importSummaryId,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
