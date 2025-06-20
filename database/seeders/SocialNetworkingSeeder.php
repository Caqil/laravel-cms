<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Post;
use App\Models\UserRelationship;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class SocialNetworkingSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('posts')) {
            return;
        }

        // Create sample posts
        $this->createSamplePosts();
        
        // Create sample relationships
        $this->createSampleRelationships();
    }

    private function createSamplePosts(): void
    {
        $users = User::limit(4)->get();
        
        $samplePosts = [
            [
                'content' => 'Welcome to our new social networking platform! 🎉 Excited to connect with everyone here.',
                'type' => 'text',
                'visibility' => 'public',
            ],
            [
                'content' => 'Just had an amazing coffee this morning ☕ What\'s everyone up to today?',
                'type' => 'text',
                'visibility' => 'public',
            ],
            [
                'content' => 'Working on some new art pieces. Can\'t wait to share them with you all! 🎨',
                'type' => 'text',
                'visibility' => 'public',
            ],
            [
                'content' => 'Beautiful sunset today 🌅 Nature never fails to amaze me.',
                'type' => 'text',
                'visibility' => 'public',
            ],
            [
                'content' => 'Loving the community features on this platform. Great work by the development team! 👏',
                'type' => 'text',
                'visibility' => 'public',
            ],
        ];

        foreach ($samplePosts as $index => $postData) {
            $user = $users[$index % $users->count()];
            
            Post::create([
                'user_id' => $user->id,
                'content' => $postData['content'],
                'type' => $postData['type'],
                'visibility' => $postData['visibility'],
                'status' => 'published',
                'published_at' => now()->subHours(rand(1, 24)),
                'comments_enabled' => true,
            ]);
        }
    }

    private function createSampleRelationships(): void
    {
        if (!Schema::hasTable('user_relationships')) {
            return;
        }

        $users = User::limit(4)->get();
        
        if ($users->count() < 2) {
            return;
        }

        // Create some follow relationships
        foreach ($users as $user) {
            foreach ($users as $otherUser) {
                if ($user->id !== $otherUser->id && rand(0, 1)) {
                    UserRelationship::firstOrCreate([
                        'follower_id' => $user->id,
                        'following_id' => $otherUser->id,
                        'type' => 'follow',
                        'status' => 'accepted',
                    ]);
                }
            }
        }

        // Update follower/following counts
        foreach ($users as $user) {
            if ($user->profile) {
                $user->profile->updateFollowersCount();
                $user->profile->updateFollowingCount();
            }
        }
    }
}
