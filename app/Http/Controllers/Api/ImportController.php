<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ImportSummary;
use App\Jobs\ProcessCsvImport;
use App\Http\Requests\CsvUploadRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportController extends Controller
{
    public function upload(CsvUploadRequest $request)
    {
        $file = $request->file('csv_file');

        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('imports', $filename);

        $importSummary = ImportSummary::create([
            'filename' => $file->getClientOriginalName(),
            'status' => ImportSummary::STATUS_PENDING,
        ]);

        ProcessCsvImport::dispatch($importSummary->id, $filePath);

        return response()->json([
            'message' => 'File uploaded successfully. Processing started.',
            'import_id' => $importSummary->id,
        ]);
    }

    public function status(ImportSummary $import)
    {
        return response()->json([
            'status' => $import->status,
            'progress' => [
                'total_rows' => $import->total_rows,
                'imported_count' => $import->imported_count,
                'duplicate_count' => $import->duplicate_count,
                'error_count' => $import->error_count,
            ],
        ]);
    }

    public function summary(ImportSummary $import)
    {
        return response()->json([
            'filename' => $import->filename,
            'status' => $import->status,
            'total_rows' => $import->total_rows,
            'imported_count' => $import->imported_count,
            'duplicate_count' => $import->duplicate_count,
            'error_count' => $import->error_count,
            'error_details' => $import->error_details,
            'created_at' => $import->created_at,
        ]);
    }
}
