<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    public function show(Page $page): Response
    {
        if ($page->status !== 'published') {
            abort(404);
        }

        return Inertia::render('Frontend/Page', [
            'page' => $page->load('author'),
        ]);
    }
}
