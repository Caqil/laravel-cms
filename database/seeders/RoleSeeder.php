<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions for social networking
        $permissions = [
            // User management
            'manage users',
            'view users',
            'edit users',
            'delete users',
            
            // Content management
            'manage posts',
            'create posts',
            'edit posts',
            'delete posts',
            'moderate posts',
            
            // Media management
            'manage media',
            'upload media',
            'delete media',
            
            // Plugin/Theme management
            'manage plugins',
            'manage themes',
            'manage pages',
            'manage settings',
            
            // Social features
            'follow users',
            'create relationships',
            'manage relationships',
            'view private profiles',
            
            // Moderation
            'moderate content',
            'handle reports',
            'ban users',
            'manage user activities',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $moderatorRole = Role::firstOrCreate(['name' => 'moderator']);
        $userRole = Role::firstOrCreate(['name' => 'user']);
        $verifiedUserRole = Role::firstOrCreate(['name' => 'verified_user']);

        // Assign permissions to roles
        $adminRole->givePermissionTo(Permission::all());
        
        $moderatorRole->givePermissionTo([
            'view users',
            'moderate posts',
            'moderate content',
            'handle reports',
            'manage user activities',
        ]);
        
        $userRole->givePermissionTo([
            'create posts',
            'edit posts',
            'upload media',
            'follow users',
            'create relationships',
        ]);
        
        $verifiedUserRole->givePermissionTo([
            'create posts',
            'edit posts',
            'upload media',
            'follow users',
            'create relationships',
            'view private profiles',
        ]);
    }
}
