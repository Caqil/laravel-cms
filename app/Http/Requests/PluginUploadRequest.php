<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PluginUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'plugin' => [
                'required',
                'file',
                'mimes:zip',
                'max:' . (config('plugins.max_upload_size', 10240000) / 1024),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'plugin.required' => 'Please select a plugin file to upload.',
            'plugin.mimes' => 'Plugin must be a ZIP file.',
            'plugin.max' => 'Plugin file size cannot exceed ' . (config('plugins.max_upload_size', 10240000) / 1024) . 'KB.',
        ];
    }
}
