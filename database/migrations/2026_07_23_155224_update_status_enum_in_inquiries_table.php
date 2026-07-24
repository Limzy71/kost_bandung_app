<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE inquiries MODIFY COLUMN status ENUM('unread', 'read', 'archived') DEFAULT 'unread'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE inquiries MODIFY COLUMN status ENUM('pending', 'contacted', 'closed') DEFAULT 'pending'");
        }
    }
};
