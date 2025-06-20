<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Post;
use App\Models\UserRelationship;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Response;

class SocialController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function users(): Response
    {
        $users = User::with(['profile', 'roles'])
                    ->withCount(['posts', 'followers', 'following'])
                    ->paginate(15);

        return Inertia::render('Admin/Social/Users', [
            'users' => $users,
        ]);
    }

    public function posts(): Response
    {
        if (!Schema::hasTable('posts')) {
            return Inertia::render('Admin/Social/Posts', [
                'posts' => ['data' => []],
                'message' => 'Posts table not available. Run migrations to enable social features.',
            ]);
        }

        $posts = Post::with(['user', 'media'])
                     ->withCount(['media'])
                     ->latest()
                     ->paginate(15);

        return Inertia::render('Admin/Social/Posts', [
            'posts' => $posts,
        ]);
    }

    public function activities(): Response
    {
        if (!Schema::hasTable('user_activities')) {
            return Inertia::render('Admin/Social/Activities', [
                'activities' => ['data' => []],
                'message' => 'Activities table not available. Run migrations to enable social features.',
            ]);
        }

        $activities = UserActivity::with(['user', 'subject'])
                                 ->latest()
                                 ->paginate(20);

        return Inertia::render('Admin/Social/Activities', [
            'activities' => $activities,
        ]);
    }

    public function relationships(): Response
    {
        if (!Schema::hasTable('user_relationships')) {
            return Inertia::render('Admin/Social/Relationships', [
                'relationships' => ['data' => []],
                'message' => 'Relationships table not available. Run migrations to enable social features.',
            ]);
        }

        $relationships = UserRelationship::with(['follower', 'following'])
                                        ->latest()
                                        ->paginate(15);

        return Inertia::render('Admin/Social/Relationships', [
            'relationships' => $relationships,
        ]);
    }

    public function moderatePost(Request $request, Post $post)
    {
        $request->validate([
            'action' => 'required|in:approve,reject,delete',
            'reason' => 'nullable|string|max:255',
        ]);

        switch ($request->action) {
            case 'approve':
                $post->update([
                    'is_reported' => false,
                    'moderated_at' => now(),
                    'moderated_by' => auth()->id(),
                ]);
                break;
                
            case 'reject':
                $post->update([
                    'status' => 'draft',
                    'moderated_at' => now(),
                    'moderated_by' => auth()->id(),
                ]);
                break;
                
            case 'delete':
                $post->delete();
                break;
        }

        // Log activity
        if (Schema::hasTable('user_activities')) {
            UserActivity::log(
                auth()->user(),
                'post_moderated',
                "Moderated post: {$request->action}",
                $post,
                ['reason' => $request->reason]
            );
        }

        return back()->with('success', 'Post moderated successfully.');
    }
}
