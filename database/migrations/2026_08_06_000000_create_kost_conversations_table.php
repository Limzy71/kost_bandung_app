<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kost_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kost_id')->constrained()->cascadeOnDelete();
            $table->foreignId('seeker_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['open', 'archived_by_owner', 'archived_by_seeker'])->default('open');
            $table->timestamps();

            $table->unique(['kost_id', 'seeker_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kost_conversations');
    }
};
