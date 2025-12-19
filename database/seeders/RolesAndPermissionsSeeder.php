<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User Management
            'view users',
            'create users',
            'edit users',
            'delete users',
            
            // Role Management
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            
            // Permission Management
            'view permissions',
            'create permissions',
            'edit permissions',
            'delete permissions',
            
            // General Admin
            'access admin panel',
            
            // Arm Dealer Management
            'view arm dealers',
            'create arm dealers',
            'edit arm dealers',
            'delete arm dealers',
            
            // Weapon Management
            'view weapons',
            'create weapons',
            'edit weapons',
            'delete weapons',
            
            // Weapon Type Management
            'view weapon types',
            'create weapon types',
            'edit weapon types',
            'delete weapon types',
            
            // Bore Management
            'view bores',
            'create bores',
            'edit bores',
            'delete bores',
            
            // Make Management
            'view makes',
            'create makes',
            'edit makes',
            'delete makes',
            
            // License Issuer Management
            'view license issuers',
            'create license issuers',
            'edit license issuers',
            'delete license issuers',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(
                ['name' => $permissionName, 'guard_name' => 'web'],
                [
                    'name' => $permissionName,
                    'guard_name' => 'web',
                    'slug' => Str::slug($permissionName),
                ]
            );
        }

        $this->command->info('Permissions created successfully.');

        // Create roles and assign permissions
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin', 'guard_name' => 'web'],
            [
                'name' => 'admin',
                'guard_name' => 'web',
                'slug' => 'admin',
                'description' => 'Administrator role with full access to all features',
            ]
        );
        $adminRole->givePermissionTo(Permission::all());
        $this->command->info('Admin role created with all permissions.');

        // User role (limited permissions)
        $userRole = Role::firstOrCreate(
            ['name' => 'user', 'guard_name' => 'web'],
            [
                'name' => 'user',
                'guard_name' => 'web',
                'slug' => 'user',
                'description' => 'Regular user role with limited access',
            ]
        );
        // User role ko koi special permission nahi de rahe (default)
        $this->command->info('User role created.');

        $this->command->info('Roles and permissions setup completed!');
    }
}

