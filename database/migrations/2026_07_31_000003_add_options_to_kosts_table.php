<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kosts', function (Blueprint $table) {
            $table->enum('rent_period', ['daily', 'weekly', 'monthly', 'yearly'])->default('monthly')->after('price_monthly');
            $table->decimal('price_deposit', 12, 2)->nullable()->after('rent_period');
            $table->boolean('include_utilities')->default(false)->after('price_deposit');
            $table->enum('bathroom_type', ['inside', 'outside', 'both'])->default('inside')->after('available_rooms');
            $table->boolean('is_furnished')->default(false)->after('bathroom_type');
            $table->string('room_size')->nullable()->after('is_furnished');
            $table->string('floor')->nullable()->after('room_size');
            $table->string('whatsapp_contact')->nullable()->after('floor');
            $table->string('nearby_landmarks')->nullable()->after('whatsapp_contact');
        });
    }

    public function down(): void
    {
        Schema::table('kosts', function (Blueprint $table) {
            $table->dropColumn([
                'rent_period',
                'price_deposit',
                'include_utilities',
                'bathroom_type',
                'is_furnished',
                'room_size',
                'floor',
                'whatsapp_contact',
                'nearby_landmarks',
            ]);
        });
    }
};
