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
        Schema::create('boost_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('kost_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('amount');
            $table->string('status')->default('pending');
            $table->string('midtrans_status')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('snap_token')->nullable();
            $table->json('midtrans_response')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boost_transactions');
    }
};
