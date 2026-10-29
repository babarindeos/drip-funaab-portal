# Conference Grant module - Step 5: emails

No migration. Copy each file to the same relative path, then:

    php artisan view:clear

Requires the earlier steps, and the mail setup you already use for the proposal
emails (Mailtrap or your real mail server in .env, DRIP_BASE_URL, and optionally
DRIP_ADMIN_EMAIL). Nothing new to configure.

## Files
NEW
- app/Mail/Conference/ConferenceSubmittedMail.php
- app/Mail/Conference/ConferenceNewApplicationAdminMail.php
- app/Mail/Conference/ConferenceDecisionMail.php
- app/Services/Conference/ConferenceNotifications.php      who gets which email
- resources/views/emails/conference/submitted.blade.php
- resources/views/emails/conference/new-application-admin.blade.php
- resources/views/emails/conference/decision.blade.php

REPLACE
- app/Http/Controllers/Conference/Staff/ConferenceApplicationController.php   sends on submit
- app/Http/Controllers/Conference/Admin/ConferenceApplicationReviewController.php   sends on decision
- resources/views/conference/admin/applications/show.blade.php   audit-trail labels

## The emails
| When | Who gets it |
|---|---|
| The applicant submits | The applicant (confirmation) and DRIP (alert) |
| The committee approves | The applicant: approved, with the call's "next steps" (e.g. the TETFund portal link) |
| The committee does not approve | The applicant: a courteous "not approved" |

- The DRIP alert shows applicant, department, category, first-time or previous
  beneficiary, the event, dates and total requested, with a button straight to
  the review page.
- The admin's remark is included ONLY if "Show this remark to the applicant" was ticked.
- Buttons use DRIP_BASE_URL, like the other emails, so links are right locally and live.

## Behaviour to know about
- **Failures never block anything.** The submission or decision is saved first;
  a mail problem is written to storage/logs/laravel.log. If the applicant could
  not be told after a decision, the admin sees a message saying so, and the audit
  trail records "Email to the applicant failed".
- **The audit trail shows "Applicant emailed"** (with the addresses) after each decision.
- **Who the applicant's emails go to:** the email of their portal account and the
  FUNAAB email they gave on the form, in one message, if the two differ.
- **DRIP alerts go to** DRIP_ADMIN_EMAIL if set (comma-separated is fine), otherwise
  to every admin account - the same rule as the proposal emails.
- **Reopening a decision sends nothing.** The applicant is emailed when the new
  decision is recorded, so they are not told twice.

## Test after applying (watch your Mailtrap inbox)
1. As staff, submit a complete application: a confirmation arrives for the applicant
   and an alert for DRIP. Check the buttons open the right pages.
2. As admin, approve it (call has "instructions for approved applicants", remark
   UNticked): the email shows Next steps and no remark. Reopen it, approve again
   with the remark TICKED: the email now includes the message.
3. Not approve another application: the wording is courteous, no next steps.
4. Open the application as admin: the audit trail has "Applicant emailed".
5. Break MAIL_PASSWORD, run `php artisan config:clear`, then (a) submit: it still
   submits; (b) decide: it is still recorded, you are told the applicant could not
   be emailed, and the trail says the email failed. Restore the password afterwards.
6. Reopen a decision: no email is sent.
