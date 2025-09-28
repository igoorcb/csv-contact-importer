<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('import_summaries', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->integer('total_rows')->default(0);
            $table->integer('imported_count')->default(0);
            $table->integer('duplicate_count')->default(0);
            $table->integer('error_count')->default(0);
            $table->string('status')->default('pending');
            $table->text('error_details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_summaries');
    }
};
