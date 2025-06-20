<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserPrivacySetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $userData = [
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ];

        // Add social networking fields if columns exist
        if (Schema::hasColumn('users', 'username')) {
            $userData['username'] = 'admin';
        }
        if (Schema::hasColumn('users', 'is_active')) {
            $userData['is_active'] = true;
        }
        if (Schema::hasColumn('users', 'is_verified')) {
            $userData['is_verified'] = true;
        }
        if (Schema::hasColumn('users', 'account_status')) {
            $userData['account_status'] = 'active';
        }

        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            $userData
        );

        // Create user profile if table exists
        if (Schema::hasTable('user_profiles')) {
            UserProfile::firstOrCreate(
                ['user_id' => $admin->id],
                [
                    'bio' => 'Administrator of this social network platform.',
                    'location' => 'Earth',
                    'profile_visibility' => 'public',
                ]
            );
        }

        // Create privacy settings if table exists
        if (Schema::hasTable('user_privacy_settings')) {
            UserPrivacySetting::firstOrCreate(
                ['user_id' => $admin->id],
                UserPrivacySetting::defaultSettings()
            );
        }

        // Assign admin role if roles exist
        try {
            if (class_exists(\Spatie\Permission\Models\Role::class)) {
                $adminRole = \Spatie\Permission\Models\Role::where('name', 'admin')->first();
                if ($adminRole && !$admin->hasRole('admin')) {
                    $admin->assignRole('admin');
                }
            }
        } catch (\Exception $e) {
            // Roles not set up yet, continue
        }

        // Create demo users for social networking
        $this->createDemoUsers();
    }

    private function createDemoUsers(): void
    {
        $demoUsers = [
            [
                'name' => 'Jane Smith',
                'username' => 'janesmith',
                'email' => 'jane@example.com',
                'bio' => 'Social media enthusiast and photographer 📸',
                'location' => 'New York, USA',
            ],
            [
                'name' => 'John Doe',
                'username' => 'johndoe',
                'email' => 'john@example.com',
                'bio' => 'Tech lover and coffee addict ☕',
                'location' => 'San Francisco, USA',
            ],
            [
                'name' => 'Alice Johnson',
                'username' => 'alicejohnson',
                'email' => 'alice@example.com',
                'bio' => 'Artist and creative soul 🎨',
                'location' => 'London, UK',
            ],
        ];

        foreach ($demoUsers as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'username' => $userData['username'] ?? null,
                    'email' => $userData['email'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'is_active' => true,
                    'account_status' => 'active',
                ]
            );

            // Create profile
            if (Schema::hasTable('user_profiles')) {
                UserProfile::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'bio' => $userData['bio'],
                        'location' => $userData['location'],
                        'profile_visibility' => 'public',
                    ]
                );
            }

            // Create privacy settings
            if (Schema::hasTable('user_privacy_settings')) {
                UserPrivacySetting::firstOrCreate(
                    ['user_id' => $user->id],
                    UserPrivacySetting::defaultSettings()
                );
            }

            // Assign user role
            try {
                if (class_exists(\Spatie\Permission\Models\Role::class)) {
                    $userRole = \Spatie\Permission\Models\Role::where('name', 'user')->first();
                    if ($userRole && !$user->hasRole('user')) {
                        $user->assignRole('user');
                    }
                }
            } catch (\Exception $e) {
                // Continue
            }
        }
    }
}
