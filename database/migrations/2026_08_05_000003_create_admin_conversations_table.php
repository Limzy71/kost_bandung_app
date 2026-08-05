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
        Schema::create('admin_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // Pengirim (pencari kost / pemilik kost)
            $table->string('sender_role'); // snapshot 'user' | 'owner'
            $table->string('category'); // komplain | pertanyaan | masukan | lainnya
            $table->string('status')->default('open'); // open | closed
            $table->string('closed_reason')->nullable(); // admin | expired
            $table->timestamp('awaiting_reply_at')->nullable(); // Jam ke-0 hitung 1x24 jam (set saat user kirim, clear saat admin balas)
            $table->timestamp('closed_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['status', 'awaiting_reply_at']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_conversations');
    }
};
