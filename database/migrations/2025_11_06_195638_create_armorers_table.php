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
        Schema::create('armorers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('address')->nullable();
            $table->string('cell')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->decimal('longitude', 10, 8)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->string('shop_name')->nullable();
            $table->string('license_number')->nullable();
            $table->date('license_expiry')->nullable();
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->string('province')->nullable();
            $table->string('postal_code')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('armorers');
    }
};
