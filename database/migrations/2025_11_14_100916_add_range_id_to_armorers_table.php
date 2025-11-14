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
        Schema::table('armorers', function (Blueprint $table) {
            $table->foreignId('range_id')->nullable()->after('postal_code')->constrained('ranges')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('armorers', function (Blueprint $table) {
            $table->dropForeign(['range_id']);
            $table->dropColumn('range_id');
        });
    }
};
