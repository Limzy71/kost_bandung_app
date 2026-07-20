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
        Schema::create('kosts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Pemilik kost
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->enum('gender_type', ['putra', 'putri', 'campur']);
            $table->decimal('price_monthly', 12, 2);
            $table->string('address');
            $table->string('district')->default('Coblong'); // Kecamatan di Bandung
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->boolean('is_available')->default(true);
            $table->timestamp('boosted_at')->nullable(); // Untuk fitur promo/fitur teratas
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kosts');
    }
};
