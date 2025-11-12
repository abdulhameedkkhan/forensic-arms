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
        // Add guard_name to roles table (required by Spatie)
        if (!Schema::hasColumn('roles', 'guard_name')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->string('guard_name')->default('web')->after('name');
            });
            
            // Update existing records
            \DB::table('roles')->update(['guard_name' => 'web']);
            
            // Make guard_name required
            Schema::table('roles', function (Blueprint $table) {
                $table->string('guard_name')->default(null)->change();
            });
        }

        // Add guard_name to permissions table (required by Spatie)
        if (!Schema::hasColumn('permissions', 'guard_name')) {
            Schema::table('permissions', function (Blueprint $table) {
                $table->string('guard_name')->default('web')->after('name');
            });
            
            // Update existing records
            \DB::table('permissions')->update(['guard_name' => 'web']);
            
            // Make guard_name required
            Schema::table('permissions', function (Blueprint $table) {
                $table->string('guard_name')->default(null)->change();
            });
        }

        // Add slug to roles table if not exists
        if (!Schema::hasColumn('roles', 'slug')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->string('slug')->nullable()->after('guard_name');
                $table->index('slug');
            });
        }
        
        // Add description to roles table if not exists
        if (!Schema::hasColumn('roles', 'description')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->text('description')->nullable()->after('slug');
            });
        }

        // Add slug to permissions table if not exists
        if (!Schema::hasColumn('permissions', 'slug')) {
            Schema::table('permissions', function (Blueprint $table) {
                $table->string('slug')->nullable()->after('guard_name');
                $table->index('slug');
            });
        }
        
        // Add description to permissions table if not exists
        if (!Schema::hasColumn('permissions', 'description')) {
            Schema::table('permissions', function (Blueprint $table) {
                $table->text('description')->nullable()->after('slug');
            });
        }

        // Update unique constraints to include guard_name (Spatie requirement)
        // Note: We'll handle unique constraints separately if needed
        // Spatie requires unique(['name', 'guard_name']) but we'll keep existing constraints for now
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            if (Schema::hasColumn('roles', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('roles', 'slug')) {
                $table->dropColumn('slug');
            }
        });

        Schema::table('permissions', function (Blueprint $table) {
            if (Schema::hasColumn('permissions', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('permissions', 'slug')) {
                $table->dropColumn('slug');
            }
        });
    }
};
