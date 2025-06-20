<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ThemeUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'theme' => [
                'required',
                'file',
                'mimes:zip',
                'max:' . (config('themes.max_upload_size', 10240000) / 1024),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'theme.required' => 'Please select a theme file to upload.',
            'theme.mimes' => 'Theme must be a ZIP file.',
            'theme.max' => 'Theme file size cannot exceed ' . (config('themes.max_upload_size', 10240000) / 1024) . 'KB.',
        ];
    }
}
