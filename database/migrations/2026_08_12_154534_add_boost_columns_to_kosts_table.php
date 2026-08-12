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
        Schema::table('kosts', function (Blueprint $table) {
            $table->string('boost_type')->nullable()->after('additional_rules_note')->comment('free_trial, paid');
            $table->timestamp('boost_expires_at')->nullable()->after('boosted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kosts', function (Blueprint $table) {
            $table->dropColumn(['boost_type', 'boost_expires_at']);
        });
    }
};
