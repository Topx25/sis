<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sector_id' => ['required', 'exists:sectors,id'],
            'title' => ['required', 'string', 'max:255'],
            'client_name' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'summary' => ['required', 'string', 'max:1000'],
            'description' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'file', 'mimes:jpeg,png,jpg,webp', 'max:' . config('image.max_size_kb', 10240)],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string'],
            'service_ids' => ['nullable', 'array'],
            'service_ids.*' => ['exists:services,id'],
            'images' => ['nullable', 'array'],
            'images.*' => ['nullable'], // Allow files or strings
            'gallery_images' => ['nullable', 'array'],
            'gallery_images.*' => ['nullable'],
            'gallery_files' => ['nullable', 'array'],
            'gallery_files.*' => ['nullable'],
            'metrics' => ['nullable', 'array'],
            'scope' => ['nullable', 'array'],
            'technologies' => ['nullable', 'array'],
            'results' => ['nullable', 'array'],
            'seo' => ['nullable'],
            'seo_og_image' => ['nullable'],
        ];
    }
}
