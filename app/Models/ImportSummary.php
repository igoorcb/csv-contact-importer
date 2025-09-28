<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportSummary extends Model
{
    protected $fillable = [
        'filename',
        'total_rows',
        'imported_count',
        'duplicate_count',
        'error_count',
        'status',
        'error_details',
    ];

    protected $casts = [
        'total_rows' => 'integer',
        'imported_count' => 'integer',
        'duplicate_count' => 'integer',
        'error_count' => 'integer',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
}
