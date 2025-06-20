<?php

namespace App\Services;

use App\Models\Plugin;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Nwidart\Modules\Facades\Module;
use ZipArchive;

class PluginService
{
    public function uploadAndInstall(UploadedFile $file): Plugin
    {
        $this->validateZipFile($file);

        $extractPath = $this->extractPlugin($file);
        $config = $this->loadModuleConfig($extractPath);
        
        $moduleName = $this->createModule($config, $extractPath);
        $plugin = $this->createPluginRecord($config, $moduleName);
        
        return $plugin;
    }

    public function createFromCommand(string $name): Plugin
    {
        $moduleName = Str::studly($name);
        
        // Generate module using artisan command
        Artisan::call('module:make', ['name' => $moduleName]);
        
        // Create default plugin configuration
        $config = [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => "A plugin module for {$name}",
            'version' => '1.0.0',
            'author' => 'Laravel CMS',
            'type' => 'plugin',
        ];
        
        return $this->createPluginRecord($config, $moduleName);
    }

    public function activate(Plugin $plugin): void
    {
        if (!$plugin->getModule()) {
            throw new \Exception('Module not found.');
        }

        if (!$plugin->hasRequiredDependencies()) {
            throw new \Exception('Missing required dependencies.');
        }

        Module::enable($plugin->module_name);
        $plugin->activate();
        
        // Run module migrations if they exist
        $this->runModuleMigrations($plugin->module_name);
    }

    public function deactivate(Plugin $plugin): void
    {
        Module::disable($plugin->module_name);
        $plugin->deactivate();
    }

    public function uninstall(Plugin $plugin): void
    {
        if ($plugin->is_active) {
            $this->deactivate($plugin);
        }

        // Remove module directory
        if ($plugin->getModule()) {
            $modulePath = $plugin->getModulePath();
            if (File::exists($modulePath)) {
                File::deleteDirectory($modulePath);
            }
        }

        $plugin->delete();
    }

    public function getAllPlugins()
    {
        return Plugin::plugins()->get();
    }

    public function getActivePlugins()
    {
        return Plugin::plugins()->active()->get();
    }

    private function validateZipFile(UploadedFile $file): void
    {
        if ($file->getClientOriginalExtension() !== 'zip') {
            throw new \Exception('File must be a ZIP archive.');
        }

        if ($file->getSize() > config('plugins.max_upload_size', 10240000)) {
            throw new \Exception('File size exceeds maximum allowed size.');
        }
    }

    private function extractPlugin(UploadedFile $file): string
    {
        $zip = new ZipArchive;
        $extractPath = storage_path('app/modules/temp_' . Str::random(10));

        if ($zip->open($file->path()) === TRUE) {
            $zip->extractTo($extractPath);
            $zip->close();
        } else {
            throw new \Exception('Could not extract ZIP file.');
        }

        return $extractPath;
    }

    private function loadModuleConfig(string $path): array
    {
        $configFile = $path . '/module.json';
        
        if (!File::exists($configFile)) {
            throw new \Exception('Module configuration file (module.json) not found.');
        }

        $config = json_decode(File::get($configFile), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid module configuration file.');
        }

        $this->validateModuleConfig($config);

        return $config;
    }

    private function validateModuleConfig(array $config): void
    {
        $required = ['name', 'slug', 'version', 'type'];
        
        foreach ($required as $field) {
            if (!isset($config[$field])) {
                throw new \Exception("Missing required field: {$field}");
            }
        }

        if (Plugin::where('slug', $config['slug'])->exists()) {
            throw new \Exception('Plugin with this slug already exists.');
        }

        if (Module::find(Str::studly($config['name']))) {
            throw new \Exception('Module with this name already exists.');
        }
    }

    private function createModule(array $config, string $tempPath): string
    {
        $moduleName = Str::studly($config['name']);
        $modulePath = base_path("Modules/{$moduleName}");
        
        if (File::exists($modulePath)) {
            File::deleteDirectory($modulePath);
        }

        File::move($tempPath, $modulePath);

        return $moduleName;
    }

    private function createPluginRecord(array $config, string $moduleName): Plugin
    {
        return Plugin::create([
            'name' => $config['name'],
            'slug' => $config['slug'],
            'description' => $config['description'] ?? '',
            'version' => $config['version'],
            'author' => $config['author'] ?? '',
            'author_url' => $config['author_url'] ?? '',
            'plugin_url' => $config['plugin_url'] ?? '',
            'module_name' => $moduleName,
            'config' => $config,
            'dependencies' => $config['dependencies'] ?? [],
            'minimum_php_version' => $config['minimum_php_version'] ?? '8.1',
            'minimum_laravel_version' => $config['minimum_laravel_version'] ?? '11.0',
            'type' => $config['type'] ?? 'plugin',
        ]);
    }

    private function runModuleMigrations(string $moduleName): void
    {
        try {
            Artisan::call('module:migrate', ['module' => $moduleName]);
        } catch (\Exception $e) {
            // Migration failed, but continue
        }
    }
}
