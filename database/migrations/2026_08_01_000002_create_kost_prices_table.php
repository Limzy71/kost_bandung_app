<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kost_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kost_id')->constrained()->cascadeOnDelete();
            $table->string('period', 20)->index();
            $table->decimal('price', 12, 2);
            $table->timestamps();

            $table->unique(['kost_id', 'period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kost_prices');
    }
};
