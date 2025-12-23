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
            // Drop the existing unique constraint on fsl_diary_no
            $table->dropUnique(['fsl_diary_no']);
            
            // Add composite unique constraint on (fsl_diary_no, range_id)
            // This allows the same fsl_diary_no to exist in different range_ids
            $table->unique(['fsl_diary_no', 'range_id'], 'weapons_fsl_diary_no_range_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('weapons', function (Blueprint $table) {
            // Drop the composite unique constraint
            $table->dropUnique('weapons_fsl_diary_no_range_id_unique');
            
            // Restore the original unique constraint on fsl_diary_no only
            $table->unique('fsl_diary_no');
        });
    }
};
