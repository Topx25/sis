<?php

namespace App\Services;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Config;

/**
 * Applies DB-stored mail settings over the runtime config just before sending.
 * When mail settings are disabled, the app's .env/config defaults are used.
 */
class MailConfigService
{
    public function apply(): void
    {
        $m = AppSetting::group('mail'); // decrypted

        if (($m['enabled'] ?? '0') !== '1') {
            return;
        }

        $mailer = ! empty($m['mailer']) ? $m['mailer'] : Config::get('mail.default');
        Config::set('mail.default', $mailer);

        if ($mailer === 'smtp') {
            if (! empty($m['host'])) {
                Config::set('mail.mailers.smtp.host', $m['host']);
            }
            if (! empty($m['port'])) {
                Config::set('mail.mailers.smtp.port', (int) $m['port']);
            }
            Config::set('mail.mailers.smtp.username', $m['username'] ?? null);
            Config::set('mail.mailers.smtp.password', $m['password'] ?? null);

            $encryption = $m['encryption'] ?? 'tls';
            Config::set('mail.mailers.smtp.encryption', ($encryption === 'none' || $encryption === '') ? null : $encryption);
        }

        if (! empty($m['from_address'])) {
            Config::set('mail.from.address', $m['from_address']);
        }
        if (! empty($m['from_name'])) {
            Config::set('mail.from.name', $m['from_name']);
        }
    }

    /**
     * Resolve the contact-notification recipient (DB setting → config → fallback).
     */
    public function recipient(): string
    {
        $to = AppSetting::getValue('mail', 'to_address');

        return ! empty($to)
            ? $to
            : (Config::get('mail.to_address') ?: Config::get('mail.from.address', 'sales@sis-eg.org'));
    }
}
