<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class UpdateAdminPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $adminRole = Role::where('name', 'admin')->first();
        
        if ($adminRole) {
            // Give admin role all permissions
            $adminRole->syncPermissions(Permission::all());
            $this->command->info('Admin role updated with all permissions (' . Permission::count() . ' permissions).');
        } else {
            $this->command->error('Admin role not found!');
        }
    }
}

