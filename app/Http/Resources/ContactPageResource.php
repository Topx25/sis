<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactPageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $emails = $this->emails;
        if (!is_array($emails) || empty($emails)) {
            if (!empty($this->email)) {
                $emails = array_values(array_filter(array_map('trim', preg_split('/[,\n;]+/', (string) $this->email))));
            } else {
                $emails = [];
            }
        }

        return [
            'id' => $this->id,
            'website' => $this->website,
            'email' => $emails[0] ?? null,
            'emails' => $emails,
            'phones' => $this->phones ?? [],
            'cta_title' => $this->getTranslations('cta_title'),
            'cta_description' => $this->getTranslations('cta_description'),
            'cta_link_text' => $this->getTranslations('cta_link_text'),
            'cta_link' => $this->cta_link,
        ];
    }
}
