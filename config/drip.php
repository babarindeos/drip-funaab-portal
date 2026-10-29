<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Public Base URL
    |--------------------------------------------------------------------------
    |
    | The address used when the portal builds links that are sent OUTSIDE the
    | app — for example the review link emailed to an external reviewer.
    |
    | Set DRIP_BASE_URL in your .env file and swap it when you deploy:
    |   local:      DRIP_BASE_URL=http://127.0.0.1:8000
    |   production: DRIP_BASE_URL=https://drip.example.edu.ng
    |
    | If DRIP_BASE_URL is not set it falls back to APP_URL.
    |
    */

    'base_url' => rtrim(env('DRIP_BASE_URL', env('APP_URL', 'http://localhost')), '/'),

    /*
    |--------------------------------------------------------------------------
    | Admin Notification Email(s)
    |--------------------------------------------------------------------------
    |
    | Who receives DRIP alerts (new application submitted, all reviewers
    | finished). One address or several separated by commas, e.g. a shared
    | DRIP inbox. If left empty, alerts go to every user whose role is 'admin'.
    |
    */

    'admin_email' => env('DRIP_ADMIN_EMAIL'),

    /*
    |--------------------------------------------------------------------------
    | Office Label
    |--------------------------------------------------------------------------
    |
    | The small line shown under the signed-in administrator's name in the header.
    |
    */

    'office_label' => env('DRIP_OFFICE_LABEL', 'FUNAAB · Research office'),

    /*
    |--------------------------------------------------------------------------
    | Proposal Documents Disk
    |--------------------------------------------------------------------------
    |
    | Where uploaded proposal documents (title page, proposal, college review)
    | are stored. This is set explicitly so uploads no longer depend on
    | FILESYSTEM_DISK.
    |
    |   public -> storage/app/public. Files are also reachable by direct URL
    |             (the current behaviour).
    |   local  -> storage/app. Files are private and can ONLY be opened
    |             through the portal's access-checked document links.
    |
    | After switching to 'local', run:  php artisan drip:move-documents
    | to move documents that were uploaded earlier.
    |
    */

    'documents_disk' => env('DRIP_DOCUMENTS_DISK', 'public'),

];
