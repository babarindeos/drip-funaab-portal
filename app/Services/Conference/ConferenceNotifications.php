<?php

namespace App\Services\Conference;

use App\Http\Classes\Notifier;
use App\Mail\Conference\ConferenceDecisionMail;
use App\Mail\Conference\ConferenceNewApplicationAdminMail;
use App\Mail\Conference\ConferenceSubmittedMail;
use App\Models\Conference\ConferenceApplication;

/**
 * The emails of the Conference Grant module.
 *
 * They are a courtesy on top of an action that has already succeeded (a
 * submission saved, a decision recorded), so none of these methods ever throws:
 * Notifier::send() logs a failure and returns false.
 */
class ConferenceNotifications
{
    /**
     * Confirmation to the applicant and an alert to DRIP.
     * Returns whether the applicant's confirmation was sent.
     */
    public static function submitted(ConferenceApplication $application): bool
    {
        $application->loadMissing('call', 'owner');

        $sent = Notifier::send(self::applicantAddresses($application), new ConferenceSubmittedMail($application), 'conference submitted - applicant');

        $admins = Notifier::admin_recipients();
        if (! empty($admins)) {
            Notifier::send($admins, new ConferenceNewApplicationAdminMail($application), 'conference submitted - admin alert');
        }

        return $sent;
    }

    /** The committee's decision, to the applicant. Returns whether it was sent. */
    public static function decided(ConferenceApplication $application): bool
    {
        $application->loadMissing('call', 'owner');

        return Notifier::send(self::applicantAddresses($application), new ConferenceDecisionMail($application), 'conference decision - applicant');
    }

    /**
     * Where the applicant is written to: the email of their portal account (what they
     * actually check for this portal) and the FUNAAB email they gave on the form, if different.
     */
    public static function applicantAddresses(ConferenceApplication $application): array
    {
        $addresses = array_filter([
            optional($application->owner)->email ?: $application->account_email,
            $application->funaab_email,
        ]);

        $unique = [];
        foreach ($addresses as $address) {
            $unique[strtolower(trim($address))] = trim($address);
        }

        return array_values($unique);
    }
}
