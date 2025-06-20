<?php

namespace App\Http\Middleware;

use App\Models\Theme;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ThemeMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $activeTheme = Theme::where('is_active', true)
                           ->where('type', 'frontend')
                           ->first();

        if ($activeTheme) {
            view()->share('activeTheme', $activeTheme);
        }

        return $next($request);
    }
}
