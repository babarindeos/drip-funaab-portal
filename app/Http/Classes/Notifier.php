<?php

namespace App\Http\Classes;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Sends the portal's notification emails.
 *
 * Notifications are a courtesy on top of an action that has already
 * succeeded (a submission saved, a status changed), so a mail failure must
 * never undo or block that action. send() therefore never throws: it logs
 * the problem and returns false.
 */
class Notifier
{
    public static function send($to, Mailable $mailable, string $context): bool
    {
        if (empty($to)) {
            Log::warning("Notification skipped ({$context}): no recipient address.");
            return false;
        }

        try {
            Mail::to($to)->send($mailable);
            return true;
        } catch (\Throwable $e) {
            Log::error("Notification failed ({$context}): ".$e->getMessage());
            return false;
        }
    }

    /**
     * Email addresses that receive admin alerts: DRIP_ADMIN_EMAIL if set
     * (comma-separated allowed), otherwise every user with the admin role.
     */
    public static function admin_recipients(): array
    {
        $configured = config('drip.admin_email');

        if (! empty($configured)) {
            return array_values(array_filter(array_map('trim', explode(',', $configured))));
        }

        return User::where('role', 'admin')->pluck('email')->filter()->values()->all();
    }

    /**
     * A full link built on the configurable public base URL (DRIP_BASE_URL),
     * so links in emails are correct in both local and production.
     */
    public static function url(string $route_name, array $parameters = []): string
    {
        return rtrim(config('drip.base_url'), '/').route($route_name, $parameters, false);
    }
}
