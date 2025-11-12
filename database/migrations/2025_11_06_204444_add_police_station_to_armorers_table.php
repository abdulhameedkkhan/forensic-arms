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
            if (!Schema::hasColumn('armorers', 'police_station')) {
                $table->string('police_station')->nullable()->after('district');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('armorers', function (Blueprint $table) {
            if (Schema::hasColumn('armorers', 'police_station')) {
                $table->dropColumn('police_station');
            }
        });
    }
};
