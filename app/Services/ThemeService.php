<?php

namespace App\Services;

use App\Models\Theme;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Nwidart\Modules\Facades\Module;
use ZipArchive;

class ThemeService
{
    public function uploadAndInstall(UploadedFile $file): Theme
    {
        $this->validateZipFile($file);

        $extractPath = $this->extractTheme($file);
        $config = $this->loadModuleConfig($extractPath);
        
        $moduleName = $this->createModule($config, $extractPath);
        $theme = $this->createThemeRecord($config, $moduleName);
        
        return $theme;
    }

    public function createFromCommand(string $name, string $type = 'frontend'): Theme
    {
        $moduleName = Str::studly($name);
        
        // Generate module using artisan command
        Artisan::call('module:make', ['name' => $moduleName]);
        
        // Create default theme configuration
        $config = [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => "A theme module for {$name}",
            'version' => '1.0.0',
            'author' => 'Laravel CMS',
            'type' => $type,
        ];
        
        return $this->createThemeRecord($config, $moduleName);
    }

    public function activate(Theme $theme): void
    {
        $theme->activate();
        $this->publishThemeAssets($theme);
    }

    public function uninstall(Theme $theme): void
    {
        if ($theme->is_active) {
            $theme->deactivate();
        }

        // Remove module directory
        if ($theme->getModule()) {
            $modulePath = $theme->getModulePath();
            if (File::exists($modulePath)) {
                File::deleteDirectory($modulePath);
            }
        }

        // Remove published assets
        $publicPath = public_path("modules/{$theme->module_name}");
        if (File::exists($publicPath)) {
            File::deleteDirectory($publicPath);
        }

        $theme->delete();
    }

    public function getAllThemes()
    {
        return Theme::themes()->get();
    }

    public function getActiveTheme(string $type = 'frontend')
    {
        return Theme::themes()->byType($type)->active()->first();
    }

    private function validateZipFile(UploadedFile $file): void
    {
        if ($file->getClientOriginalExtension() !== 'zip') {
            throw new \Exception('File must be a ZIP archive.');
        }

        if ($file->getSize() > config('themes.max_upload_size', 10240000)) {
            throw new \Exception('File size exceeds maximum allowed size.');
        }
    }

    private function extractTheme(UploadedFile $file): string
    {
        $zip = new ZipArchive;
        $extractPath = storage_path('app/themes/temp_' . Str::random(10));

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

        if (Theme::where('slug', $config['slug'])->exists()) {
            throw new \Exception('Theme with this slug already exists.');
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

    private function createThemeRecord(array $config, string $moduleName): Theme
    {
        return Theme::create([
            'name' => $config['name'],
            'slug' => $config['slug'],
            'description' => $config['description'] ?? '',
            'version' => $config['version'],
            'author' => $config['author'] ?? '',
            'author_url' => $config['author_url'] ?? '',
            'theme_url' => $config['theme_url'] ?? '',
            'screenshot' => $config['screenshot'] ?? '',
            'module_name' => $moduleName,
            'config' => $config,
            'type' => $config['type'] ?? 'frontend',
            'customization_options' => $config['customization_options'] ?? [],
        ]);
    }

    private function publishThemeAssets(Theme $theme): void
    {
        if (!$theme->module_name) return;

        $sourcePath = base_path("Modules/{$theme->module_name}/Resources/assets");
        $publicPath = public_path("modules/{$theme->module_name}");

        if (File::exists($sourcePath)) {
            if (File::exists($publicPath)) {
                File::deleteDirectory($publicPath);
            }
            
            File::copyDirectory($sourcePath, $publicPath);
        }
    }
}
