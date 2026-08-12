<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE kosts DROP CONSTRAINT IF EXISTS kosts_rent_period_check;');
            DB::statement('ALTER TABLE kosts ALTER COLUMN rent_period TYPE VARCHAR(255);');
            DB::statement("ALTER TABLE kosts ALTER COLUMN rent_period SET DEFAULT 'monthly';");
            DB::statement("ALTER TABLE kosts ADD CONSTRAINT kosts_rent_period_check CHECK (rent_period IN ('daily', 'weekly', 'monthly', 'three_monthly', 'six_monthly', 'yearly'));");
        } else {
            Schema::table('kosts', function (Blueprint $table) {
                $table->enum('rent_period', ['daily', 'weekly', 'monthly', 'three_monthly', 'six_monthly', 'yearly'])
                    ->default('monthly')
                    ->change();
            });
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE kosts DROP CONSTRAINT IF EXISTS kosts_rent_period_check;');
            DB::statement('ALTER TABLE kosts ALTER COLUMN rent_period TYPE VARCHAR(255);');
            DB::statement("ALTER TABLE kosts ALTER COLUMN rent_period SET DEFAULT 'monthly';");
            DB::statement("ALTER TABLE kosts ADD CONSTRAINT kosts_rent_period_check CHECK (rent_period IN ('daily', 'weekly', 'monthly', 'yearly'));");
        } else {
            Schema::table('kosts', function (Blueprint $table) {
                $table->enum('rent_period', ['daily', 'weekly', 'monthly', 'yearly'])
                    ->default('monthly')
                    ->change();
            });
        }
    }
};
