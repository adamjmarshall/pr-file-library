<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('file_library_files', function (Blueprint $table) {
            $table->foreignId('folder_id')->nullable()->after('id')->constrained('file_library_folders')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('file_library_files', function (Blueprint $table) {
            $table->dropConstrainedForeignId('folder_id');
        });
    }
};
