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
        // First, convert existing string CNIC values to JSON arrays
        DB::table('weapons')
            ->whereNotNull('cnic')
            ->get()
            ->each(function ($weapon) {
                $cnicValue = $weapon->cnic;
                // If it's already JSON, skip. Otherwise convert string to array
                if (!is_null($cnicValue) && !empty($cnicValue)) {
                    $jsonValue = json_encode([$cnicValue]);
                    DB::table('weapons')
                        ->where('id', $weapon->id)
                        ->update(['cnic' => $jsonValue]);
                }
            });
        
        Schema::table('weapons', function (Blueprint $table) {
            // Drop the unique index on cnic
            $table->dropUnique(['cnic']);
            
            // Change cnic column to JSON to store multiple values
            $table->json('cnic')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('weapons', function (Blueprint $table) {
            // Change back to string
            $table->string('cnic')->change();
            
            // Add unique constraint back
            $table->unique('cnic');
        });
    }
};
