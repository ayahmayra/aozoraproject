<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
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
            
            // Student Management
            'view students',
            'create students',
            'edit students',
            'delete students',
            
            // Teacher Management
            'view teachers',
            'create teachers',
            'edit teachers',
            'delete teachers',
            
            // Parent Management
            'view parents',
            'create parents',
            'edit parents',
            'delete parents',
            
            // Dashboard Access
            'view admin dashboard',
            'view teacher dashboard',
            'view parent dashboard',
            'view student dashboard',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        $teacherRole = Role::create(['name' => 'teacher']);
        $teacherRole->givePermissionTo([
            'view teacher dashboard',
            'view students',
            'view parents',
        ]);

        $parentRole = Role::create(['name' => 'parent']);
        $parentRole->givePermissionTo([
            'view parent dashboard',
            'view students',
        ]);

        $studentRole = Role::create(['name' => 'student']);
        $studentRole->givePermissionTo([
            'view student dashboard',
        ]);
    }
}