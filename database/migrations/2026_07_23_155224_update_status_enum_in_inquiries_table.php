<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            $this->rebuildTable(['unread', 'read', 'archived'], 'unread');
        } else {
            DB::statement("ALTER TABLE inquiries MODIFY COLUMN status ENUM('unread', 'read', 'archived') DEFAULT 'unread'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            $this->rebuildTable(['pending', 'contacted', 'closed'], 'pending');
        } else {
            DB::statement("ALTER TABLE inquiries MODIFY COLUMN status ENUM('pending', 'contacted', 'closed') DEFAULT 'pending'");
        }
    }

    protected function rebuildTable(array $values, string $default): void
    {
        Schema::create('inquiries_temp', function (Blueprint $table) use ($values, $default) {
            $table->id();
            $table->foreignId('kost_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('phone_number');
            $table->text('message');
            $table->enum('status', $values)->default($default);
            $table->timestamp('contacted_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        DB::statement('INSERT INTO inquiries_temp (id, kost_id, user_id, name, phone_number, message, status, contacted_at, created_at, updated_at, deleted_at) SELECT id, kost_id, user_id, name, phone_number, message, status, contacted_at, created_at, updated_at, deleted_at FROM inquiries');

        Schema::drop('inquiries');
        Schema::rename('inquiries_temp', 'inquiries');
    }
};
