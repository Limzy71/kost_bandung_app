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
            $table->string('ownership_doc_type')->nullable()->after('additional_rules_note');
            $table->string('ownership_doc_path')->nullable()->after('ownership_doc_type');
            $table->string('ownership_verification_status')->default('unverified')->after('ownership_doc_path');
            $table->timestamp('ownership_verified_at')->nullable()->after('ownership_verification_status');
            $table->string('ownership_rejection_note')->nullable()->after('ownership_verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kosts', function (Blueprint $table) {
            $table->dropColumn([
                'ownership_doc_type',
                'ownership_doc_path',
                'ownership_verification_status',
                'ownership_verified_at',
                'ownership_rejection_note',
            ]);
        });
    }
};
