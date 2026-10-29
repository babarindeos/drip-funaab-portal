<?php

namespace App\Listeners;

use Illuminate\Mail\Events\MessageSending;

/**
 * Puts the FUNAAB crest INSIDE every email that shows it, instead of linking to it on the website.
 *
 * The email header refers to the picture as "cid:drip-logo". Just before an email is sent, this
 * attaches the picture under that name, so it shows in Gmail, Outlook and Mailtrap even while the
 * portal runs on 127.0.0.1, and without the reader having to "show images".
 */
class EmbedEmailLogo
{
    public const CID = 'drip-logo';

    public function handle(MessageSending $event): void
    {
        $email = $event->message;
        $path = public_path('images/funaab_logo.png');

        if (! is_file($path) || ! str_contains((string) $email->getHtmlBody(), 'cid:'.self::CID)) {
            return;
        }

        $email->embedFromPath($path, self::CID, 'image/png');
    }
}
