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
        Schema::table('users', function (Blueprint $table) {
            $table->string('identity_doc_path')->nullable()->after('business_name');
            $table->string('identity_verification_status')->default('unverified')->after('identity_doc_path');
            $table->timestamp('identity_verified_at')->nullable()->after('identity_verification_status');
            $table->string('identity_rejection_note')->nullable()->after('identity_verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'identity_doc_path',
                'identity_verification_status',
                'identity_verified_at',
                'identity_rejection_note',
            ]);
        });
    }
};
