<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ];

        // Add optional fields only if columns exist
        if (Schema::hasColumn('users', 'is_active')) {
            $userData['is_active'] = true;
        }

        $user = User::create($userData);

        // Assign default role if roles exist
        try {
            if (class_exists(\Spatie\Permission\Models\Role::class)) {
                $userRole = \Spatie\Permission\Models\Role::where('name', 'user')->first();
                if ($userRole) {
                    $user->assignRole('user');
                }
            }
        } catch (\Exception $e) {
            // Roles not set up yet, continue
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect('/');
    }
}
