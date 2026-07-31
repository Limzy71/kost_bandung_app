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
            $table->dropColumn(['bathroom_type', 'is_furnished', 'room_size', 'floor']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kosts', function (Blueprint $table) {
            $table->enum('bathroom_type', ['inside', 'outside', 'both'])->default('inside')->after('available_rooms');
            $table->boolean('is_furnished')->default(false)->after('bathroom_type');
            $table->string('room_size')->nullable()->after('is_furnished');
            $table->string('floor')->nullable()->after('room_size');
        });
    }
};
