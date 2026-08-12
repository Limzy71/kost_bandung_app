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
        Schema::create('boost_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kost_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('reminder_type');
            $table->timestamp('boost_expires_at');
            $table->timestamp('sent_at')->useCurrent();
            $table->timestamps();

            $table->unique(['kost_id', 'reminder_type', 'boost_expires_at'], 'boost_reminders_unique_composite');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boost_reminders');
    }
};
