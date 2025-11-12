<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;

class AssignAdminRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin role create karo ya find karo (Spatie uses 'name' and 'guard_name')
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin', 'guard_name' => 'web'],
            ['name' => 'admin', 'guard_name' => 'web']
        );

        // User ko find karo (email se ya first user)
        // Yahan email change kar sakte ho jo user ko admin banana hai
        $user = User::where('email', 'admin@forensic.com')
            ->orWhere('email', 'admin@example.com')
            ->first();
        
        // Agar koi bhi nahi mila, to first user ko le lo
        if (!$user) {
            $user = User::first();
        }

        if ($user && $adminRole) {
            // User ko admin role assign karo (Spatie method)
            if (!$user->hasRole('admin')) {
                $user->assignRole($adminRole);
                $this->command->info("Admin role assigned to user: {$user->email}");
            } else {
                $this->command->info("User {$user->email} already has admin role.");
            }
        } else {
            $this->command->error("User or Admin role not found!");
        }
    }
}
