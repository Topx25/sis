<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReorderFooterLinksRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'links' => ['required_without:ids', 'array'],
            'links.*.id' => ['required_with:links', 'integer', 'exists:footer_links,id'],
            'links.*.sort_order' => ['required_with:links', 'integer', 'min:0'],
            'ids' => ['required_without:links', 'array'],
            'ids.*' => ['integer', 'exists:footer_links,id'],
        ];
    }
}
