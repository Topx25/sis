<?php

namespace App\Http\Requests;

use App\Rules\SafeIconIdentifier;
use App\Rules\SafeLinkUrl;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFooterLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category' => ['sometimes', 'string', 'in:navigation,contact'],
            'type' => ['nullable', 'string', 'in:navigation,social,phone,whatsapp,email,custom'],
            'label' => ['sometimes', 'required', 'string', 'max:255'],
            'url' => ['sometimes', 'required', 'string', 'max:1000', new SafeLinkUrl()],
            'target' => ['nullable', 'string', 'in:_self,_blank'],
            'icon' => ['nullable', 'string', new SafeIconIdentifier()],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
