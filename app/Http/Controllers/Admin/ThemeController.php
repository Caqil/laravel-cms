<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ThemeUploadRequest;
use App\Models\Theme;
use App\Services\ThemeService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ThemeController extends Controller
{
    public function __construct(
        private ThemeService $themeService
    ) {
        $this->middleware(['auth', 'admin']);
    }

    public function index(): Response
    {
        $themes = Theme::orderBy('name')->get();

        return Inertia::render('Admin/Themes/Index', [
            'themes' => $themes,
            'activeTheme' => Theme::where('is_active', true)->first(),
        ]);
    }

    public function upload(): Response
    {
        return Inertia::render('Admin/Themes/Upload');
    }

    public function store(ThemeUploadRequest $request)
    {
        try {
            $theme = $this->themeService->uploadAndInstall($request->file('theme'));

            return redirect()->route('admin.themes.index')
                ->with('success', "Theme '{$theme->name}' uploaded successfully.");
        } catch (\Exception $e) {
            return back()->withErrors(['theme' => $e->getMessage()]);
        }
    }

    public function activate(Theme $theme)
    {
        try {
            $this->themeService->activate($theme);

            return back()->with('success', "Theme '{$theme->name}' activated successfully.");
        } catch (\Exception $e) {
            return back()->withErrors(['theme' => $e->getMessage()]);
        }
    }

    public function destroy(Theme $theme)
    {
        try {
            $this->themeService->uninstall($theme);

            return back()->with('success', "Theme '{$theme->name}' uninstalled successfully.");
        } catch (\Exception $e) {
            return back()->withErrors(['theme' => $e->getMessage()]);
        }
    }
}
