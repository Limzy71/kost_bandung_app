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
            $table->enum('status', ['pending', 'published', 'rejected'])->default('pending')->after('is_available');
            $table->integer('total_rooms')->default(0)->after('status');
            $table->integer('available_rooms')->default(0)->after('total_rooms');
            $table->text('additional_rules_note')->nullable()->after('available_rooms');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kosts', function (Blueprint $table) {
            $table->dropColumn(['status', 'total_rooms', 'available_rooms', 'additional_rules_note']);
        });
    }
};
