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
        Schema::table('weapons', function (Blueprint $table) {
            // Add indexes for foreign key columns to improve dropdown performance
            $table->index('arm_dealer_id');
            $table->index('weapon_type_id');
            $table->index('bore_id');
            $table->index('make_id');
            $table->index('license_issuer_id');
            $table->index('range_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('weapons', function (Blueprint $table) {
            // Drop the indexes
            $table->dropIndex(['arm_dealer_id']);
            $table->dropIndex(['weapon_type_id']);
            $table->dropIndex(['bore_id']);
            $table->dropIndex(['make_id']);
            $table->dropIndex(['license_issuer_id']);
            $table->dropIndex(['range_id']);
        });
    }
};