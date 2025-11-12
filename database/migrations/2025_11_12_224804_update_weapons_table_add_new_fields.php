<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, update any null fsl_diary_no values (if any exist) - do this outside schema operations
        DB::table('weapons')
            ->whereNull('fsl_diary_no')
            ->get()
            ->each(function ($weapon) {
                $year = date('y');
                DB::table('weapons')
                    ->where('id', $weapon->id)
                    ->update(['fsl_diary_no' => "{$weapon->id}/{$year}"]);
            });
        
        Schema::table('weapons', function (Blueprint $table) {
            // Make fsl_diary_no required and unique
            $table->string('fsl_diary_no')->nullable(false)->unique()->change();
            
            // Add new fields
            $table->string('license_no')->nullable()->after('fsl_diary_no');
            $table->foreignId('weapon_type_id')->nullable()->constrained('weapon_types')->onDelete('set null')->after('license_no');
            $table->foreignId('bore_id')->nullable()->constrained('bores')->onDelete('set null')->after('weapon_type_id');
            $table->foreignId('make_id')->nullable()->constrained('makes')->onDelete('set null')->after('bore_id');
            $table->foreignId('license_issuer_id')->nullable()->constrained('license_issuers')->onDelete('set null')->after('make_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('weapons', function (Blueprint $table) {
            $table->dropForeign(['weapon_type_id']);
            $table->dropForeign(['bore_id']);
            $table->dropForeign(['make_id']);
            $table->dropForeign(['license_issuer_id']);
            
            $table->dropColumn(['license_no', 'weapon_type_id', 'bore_id', 'make_id', 'license_issuer_id']);
            
            // Remove unique constraint from fsl_diary_no
            $table->string('fsl_diary_no')->nullable()->change();
        });
    }
};
