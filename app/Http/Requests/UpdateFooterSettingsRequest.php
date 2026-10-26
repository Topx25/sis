<?php

namespace App\Http\Requests;

use App\Rules\SafeIconIdentifier;
use App\Rules\SafeLinkUrl;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFooterSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'logo' => [
                'nullable',
                'file',
                'mimes:jpeg,png,jpg,webp',
                'max:' . config('image.max_size_kb', 10240),
            ],
            'remove_logo' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string', 'max:2000'],
            'copyright_text' => ['nullable', 'string', 'max:255'],
            'dynamic_year' => ['nullable', 'boolean'],
            'floating_contact_enabled' => ['nullable', 'boolean'],
            'floating_contact_url' => ['nullable', 'string', 'max:1000', new SafeLinkUrl()],
            'floating_contact_icon' => ['nullable', 'string', new SafeIconIdentifier()],
            'floating_contact_label' => ['nullable', 'string', 'max:255'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
