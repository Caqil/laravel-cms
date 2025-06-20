<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        $pages = Page::published()->orderBy('sort_order')->limit(6)->get();

        return Inertia::render('Frontend/Home', [
            'pages' => $pages,
        ]);
    }
}
