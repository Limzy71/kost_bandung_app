<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kosts', function (Blueprint $table) {
            $table->enum('rent_period', ['daily', 'weekly', 'monthly', 'three_monthly', 'six_monthly', 'yearly'])
                ->default('monthly')
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('kosts', function (Blueprint $table) {
            $table->enum('rent_period', ['daily', 'weekly', 'monthly', 'yearly'])
                ->default('monthly')
                ->change();
        });
    }
};
