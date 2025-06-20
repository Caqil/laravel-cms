<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PluginUploadRequest;
use App\Models\Plugin;
use App\Services\PluginService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PluginController extends Controller
{
    public function __construct(
        private PluginService $pluginService
    ) {
        $this->middleware(['auth', 'admin']);
    }

    public function index(): Response
    {
        $plugins = Plugin::orderBy('name')->paginate(15);

        return Inertia::render('Admin/Plugins/Index', [
            'plugins' => $plugins,
        ]);
    }

    public function upload(): Response
    {
        return Inertia::render('Admin/Plugins/Upload');
    }

    public function store(PluginUploadRequest $request)
    {
        try {
            $plugin = $this->pluginService->uploadAndInstall($request->file('plugin'));

            return redirect()->route('admin.plugins.index')
                ->with('success', "Plugin '{$plugin->name}' uploaded successfully.");
        } catch (\Exception $e) {
            return back()->withErrors(['plugin' => $e->getMessage()]);
        }
    }

    public function activate(Plugin $plugin)
    {
        try {
            if (!$plugin->hasRequiredDependencies()) {
                return back()->withErrors(['plugin' => 'Missing required dependencies.']);
            }

            $this->pluginService->activate($plugin);

            return back()->with('success', "Plugin '{$plugin->name}' activated successfully.");
        } catch (\Exception $e) {
            return back()->withErrors(['plugin' => $e->getMessage()]);
        }
    }

    public function deactivate(Plugin $plugin)
    {
        try {
            $this->pluginService->deactivate($plugin);

            return back()->with('success', "Plugin '{$plugin->name}' deactivated successfully.");
        } catch (\Exception $e) {
            return back()->withErrors(['plugin' => $e->getMessage()]);
        }
    }

    public function destroy(Plugin $plugin)
    {
        try {
            $this->pluginService->uninstall($plugin);

            return back()->with('success', "Plugin '{$plugin->name}' uninstalled successfully.");
        } catch (\Exception $e) {
            return back()->withErrors(['plugin' => $e->getMessage()]);
        }
    }
}
