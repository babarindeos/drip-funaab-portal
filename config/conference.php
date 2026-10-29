<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Conference Grant module settings
    |--------------------------------------------------------------------------
    |
    | The Conference Grant module keeps its data in its own database. The
    | connection named below is defined in config/database.php (see
    | INTEGRATION.md) and reads CONF_DB_* values from .env.
    |
    */

    'connection' => 'conference',

    // Printed on the TETFund nomination form ("Name of Beneficiary Institution").
    'institution' => env('CONF_INSTITUTION', 'Federal University of Agriculture, Abeokuta'),

    /*
    | Where uploaded documents are stored. 'local' (storage/app) is PRIVATE:
    | files can only be opened through the portal's access-checked links.
    | Passport photographs, bank details and signed forms are sensitive, so
    | this module never uses the public disk.
    */
    'documents_disk' => env('CONF_DOCUMENTS_DISK', 'local'),
    'documents_folder' => 'conference',

    /*
    | Eligibility: staff with this many completed years of service or MORE may apply.
    | 3 means that two years of service or less is not qualified.
    */
    'min_service_years' => 3,

    'max_document_kb' => 20480,   // 20 MB per document
    'max_photo_kb' => 2048,       // 2 MB passport photograph

    'staff_categories' => [
        'teaching' => 'Teaching Staff',
        'non_teaching' => 'Non-Teaching Staff',
    ],

    'currencies' => [
        'NGN' => 'NGN - Naira',
        'USD' => 'USD - US Dollar',
        'GBP' => 'GBP - Pound Sterling',
        'EUR' => 'EUR - Euro',
    ],

    /*
    | Documents an application can carry. "required" says who must provide it:
    |   all                  every applicant
    |   teaching             teaching (academic) staff only
    |   previous_beneficiary applicants previously sponsored by TETFund for an
    |                        international conference (evidence of publications)
    */
    'document_types' => [
        'passport_photo' => [
            'label' => 'Passport photograph', 'required' => 'all', 'kind' => 'image',
            'extensions' => ['jpg', 'jpeg', 'png'], 'max_kb' => 2048,
            'help' => 'A recent passport-size photograph (JPG or PNG, up to 2 MB). It is printed on the nomination form.',
        ],
        'invitation_letter' => [
            'label' => 'Letter of Invitation', 'required' => 'all', 'kind' => 'document',
            'extensions' => ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'], 'max_kb' => 20480,
            'help' => 'The invitation from the conference or workshop organiser. This is compulsory.',
        ],
        'acceptance_letter' => [
            'label' => 'Letter of Acceptance of Paper', 'required' => 'teaching', 'kind' => 'document',
            'extensions' => ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'], 'max_kb' => 20480,
            'help' => 'Teaching staff only: the organiser\'s letter accepting your paper.',
        ],
        'abstract' => [
            'label' => 'Abstract of Paper', 'required' => 'teaching', 'kind' => 'document',
            'extensions' => ['pdf', 'doc', 'docx'], 'max_kb' => 20480,
            'help' => 'Teaching staff only: the abstract of the paper you will present.',
        ],
        'announcement' => [
            'label' => 'Conference / Workshop Announcement', 'required' => 'all', 'kind' => 'document',
            'extensions' => ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'], 'max_kb' => 20480,
            'help' => 'The announcement showing the venue address, web page, email and the costs of registration, flight, accommodation, feeding and excursion.',
        ],
        'publication_evidence' => [
            'label' => 'Evidence of Publications (1 Q1 or 2 Q2 articles)', 'required' => 'previous_beneficiary', 'kind' => 'document',
            'extensions' => ['pdf', 'doc', 'docx'], 'max_kb' => 20480,
            'help' => 'Because you have been sponsored by TETFund before: proof of one Q1 or two Q2 articles.',
        ],
        'confirmation_letter' => [
            'label' => 'Letter of Confirmation of Appointment', 'required' => 'confirmed', 'kind' => 'document',
            'extensions' => ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'], 'max_kb' => 20480,
            'help' => 'The letter confirming your appointment as a member of staff.',
        ],
        'permission_request' => [
            'label' => 'Request for Permission to Attend', 'required' => 'all', 'kind' => 'document',
            'extensions' => ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'], 'max_kb' => 20480,
            'help' => 'Your letter requesting permission to attend, showing the approval it received, so we can see that you are permitted to apply and go.',
        ],
    ],

];
