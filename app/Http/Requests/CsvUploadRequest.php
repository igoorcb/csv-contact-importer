<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CsvUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'csv_file' => [
                'required',
                'file',
                'mimes:csv,txt',
                'max:51200',
                function ($attribute, $value, $fail) {
                    if (!$this->isValidCsvStructure($value)) {
                        $fail('The CSV file must contain at least "name" and "email" columns.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'csv_file.required' => 'Please select a CSV file to upload.',
            'csv_file.file' => 'The uploaded file is not valid.',
            'csv_file.mimes' => 'Only CSV files are allowed.',
            'csv_file.max' => 'The file size must not exceed 50MB.',
        ];
    }

    private function isValidCsvStructure($file): bool
    {
        try {
            $handle = fopen($file->getPathname(), 'r');

            $separators = [',', ';', "\t"];
            $header = null;

            foreach ($separators as $separator) {
                rewind($handle);
                $testHeader = fgetcsv($handle, 0, $separator);

                if ($testHeader && count($testHeader) >= 2) {
                    $header = $testHeader;
                    break;
                }
            }

            fclose($handle);

            if (!$header || count($header) < 2) {
                return false;
            }

            $normalizedHeader = array_map('strtolower', array_map('trim', $header));
            $requiredColumns = ['name', 'email'];

            foreach ($requiredColumns as $column) {
                if (!in_array($column, $normalizedHeader)) {
                    return false;
                }
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
