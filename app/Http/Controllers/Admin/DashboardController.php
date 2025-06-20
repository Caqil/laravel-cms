<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plugin;
use App\Models\Theme;
use App\Models\User;
use App\Models\Page;
use App\Models\Post;
use App\Models\Media;
use App\Models\UserActivity;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(): Response
    {
        $stats = [
            'users' => User::count(),
            'active_users' => User::active()->count(),
            'verified_users' => Schema::hasColumn('users', 'is_verified') ? User::where('is_verified', true)->count() : 0,
            'plugins' => Plugin::count(),
            'active_plugins' => Plugin::where('is_active', true)->count(),
            'themes' => Theme::count(),
            'pages' => Page::count(),
        ];

        // Add social networking stats if tables exist
        if (Schema::hasTable('posts')) {
            $stats['posts'] = Post::count();
            $stats['published_posts'] = Post::published()->count();
        }

        if (Schema::hasTable('media')) {
            $stats['media_files'] = Media::count();
            $stats['images'] = Media::where('type', 'image')->count();
        }

        if (Schema::hasTable('user_relationships')) {
            $stats['total_follows'] = \App\Models\UserRelationship::where('type', 'follow')->count();
        }

        // Recent activity
        $recentUsers = User::latest()->limit(5)->get();
        $recentPlugins = Plugin::latest()->limit(5)->get();
        $recentThemes = Theme::latest()->limit(5)->get();
        
        $recentPosts = [];
        if (Schema::hasTable('posts')) {
            $recentPosts = Post::with('user')->published()->latest()->limit(5)->get();
        }

        $recentActivities = [];
        if (Schema::hasTable('user_activities')) {
            $recentActivities = UserActivity::with('user')->latest()->limit(10)->get();
        }

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentUsers' => $recentUsers,
            'recentPlugins' => $recentPlugins,
            'recentThemes' => $recentThemes,
            'recentPosts' => $recentPosts,
            'recentActivities' => $recentActivities,
        ]);
    }
}
