<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function show(Request $request, User $user): Response
    {
        // Check if user can view this profile
        $canView = $this->canViewProfile($user, $request->user());
        
        if (!$canView) {
            abort(403, 'This profile is private.');
        }

        // Load user relationships
        $user->load(['profile', 'privacySettings']);
        
        // Get user's posts if posts table exists
        $posts = [];
        if (Schema::hasTable('posts')) {
            $postsQuery = Post::where('user_id', $user->id)
                             ->published()
                             ->with(['media'])
                             ->latest();
            
            // Filter posts based on visibility and relationship
            if (!$request->user() || $request->user()->id !== $user->id) {
                $postsQuery->where('visibility', 'public');
            }
            
            $posts = $postsQuery->paginate(10);
        }

        // Get follow statistics
        $followStats = [
            'followers_count' => $user->followers()->count(),
            'following_count' => $user->following()->count(),
            'is_following' => $request->user() ? $request->user()->isFollowing($user) : false,
            'is_followed_by' => $request->user() ? $user->isFollowing($request->user()) : false,
        ];

        return Inertia::render('Profile/Show', [
            'user' => $user,
            'posts' => $posts,
            'followStats' => $followStats,
            'canEdit' => $request->user() && $request->user()->id === $user->id,
        ]);
    }

    public function edit(Request $request): Response
    {
        $user = $request->user()->load(['profile', 'privacySettings']);
        
        return Inertia::render('Profile/Edit', [
            'user' => $user,
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'bio' => 'nullable|string|max:500',
            'location' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'birth_date' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other,prefer_not_to_say',
        ]);

        // Update user
        $user->update($request->only(['name', 'username', 'email']));
        
        // Update profile if table exists
        if (Schema::hasTable('user_profiles') && $user->profile) {
            $user->profile->update($request->only([
                'bio', 'location', 'website', 'birth_date', 'gender'
            ]));
        }

        return back()->with('success', 'Profile updated successfully.');
    }

    public function follow(Request $request, User $user)
    {
        if (!$request->user()) {
            return response()->json(['error' => 'Authentication required'], 401);
        }

        if ($request->user()->id === $user->id) {
            return response()->json(['error' => 'Cannot follow yourself'], 400);
        }

        $request->user()->follow($user);
        
        // Update counts
        if ($user->profile) {
            $user->profile->updateFollowersCount();
        }
        if ($request->user()->profile) {
            $request->user()->profile->updateFollowingCount();
        }

        return response()->json(['success' => true, 'message' => 'User followed']);
    }

    public function unfollow(Request $request, User $user)
    {
        if (!$request->user()) {
            return response()->json(['error' => 'Authentication required'], 401);
        }

        $request->user()->unfollow($user);
        
        // Update counts
        if ($user->profile) {
            $user->profile->updateFollowersCount();
        }
        if ($request->user()->profile) {
            $request->user()->profile->updateFollowingCount();
        }

        return response()->json(['success' => true, 'message' => 'User unfollowed']);
    }

    private function canViewProfile(User $user, ?User $viewer): bool
    {
        if (!$user->privacySettings) {
            return true; // Default to public if no privacy settings
        }

        $visibility = $user->privacySettings->profile_visibility;
        
        switch ($visibility) {
            case 'public':
                return true;
            case 'private':
                return $viewer && $viewer->id === $user->id;
            case 'friends':
                return $viewer && ($viewer->id === $user->id || $viewer->isFollowing($user));
            default:
                return true;
        }
    }
}
