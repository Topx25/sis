<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSeoSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'canonical_base_url'        => ['nullable', 'string', 'max:255'],
            'google_site_verification'  => ['nullable', 'string', 'max:255'],
            'bing_site_verification'    => ['nullable', 'string', 'max:255'],
            'default_noindex'           => ['nullable'],
            'site_name'                 => ['nullable'],
            'site_name.en'              => ['nullable', 'string', 'max:150'],
            'site_name.ar'              => ['nullable', 'string', 'max:150'],
            'title_suffix'              => ['nullable'],
            'title_suffix.en'           => ['nullable', 'string', 'max:150'],
            'title_suffix.ar'           => ['nullable', 'string', 'max:150'],
            'default_meta_description'  => ['nullable'],
            'default_meta_description.en' => ['nullable', 'string', 'max:350'],
            'default_meta_description.ar' => ['nullable', 'string', 'max:350'],
            'default_meta_keywords'     => ['nullable'],
            'default_meta_keywords.en'  => ['nullable', 'string', 'max:500'],
            'default_meta_keywords.ar'  => ['nullable', 'string', 'max:500'],
            'default_og_image'          => ['nullable'],
            'remove_default_og_image'   => ['nullable'],
            'organization_legal_name'   => ['nullable', 'string', 'max:255'],
            'organization_phone'        => ['nullable', 'string', 'max:100'],
            'organization_email'        => ['nullable', 'email', 'max:255'],
            'social_links'              => ['nullable'],
        ];
    }
}
