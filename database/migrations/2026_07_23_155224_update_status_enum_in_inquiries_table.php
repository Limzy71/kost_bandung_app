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
        // First delete any rows that might cause issues with enum change (if we want a clean state)
        // Or simply alter the table
        DB::statement("ALTER TABLE inquiries MODIFY COLUMN status ENUM('unread', 'read', 'archived') DEFAULT 'unread'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE inquiries MODIFY COLUMN status ENUM('pending', 'contacted', 'closed') DEFAULT 'pending'");
    }
};
