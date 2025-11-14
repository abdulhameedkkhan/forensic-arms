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
        // First, convert existing JSON data to comma-separated string
        DB::table('weapons')
            ->whereNotNull('cnic')
            ->get()
            ->each(function ($weapon) {
                $cnicValue = $weapon->cnic;
                // If it's JSON, decode it
                if (is_string($cnicValue)) {
                    $decoded = json_decode($cnicValue, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $cnicValue = implode(', ', $decoded);
                    }
                } elseif (is_array($cnicValue)) {
                    $cnicValue = implode(', ', $cnicValue);
                }
                
                DB::table('weapons')
                    ->where('id', $weapon->id)
                    ->update(['cnic' => $cnicValue]);
            });
        
        Schema::table('weapons', function (Blueprint $table) {
            // Change CNIC from JSON to TEXT
            $table->text('cnic')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('weapons', function (Blueprint $table) {
            // Change back to JSON
            $table->json('cnic')->nullable()->change();
        });
    }
};
