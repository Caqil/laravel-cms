<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmailVerificationPromptController extends Controller
{
    public function __invoke(Request $request): Response
    {
        return $request->user()->hasVerifiedEmail()
                    ? redirect()->intended(route('home'))
                    : Inertia::render('Auth/VerifyEmail');
    }
}
