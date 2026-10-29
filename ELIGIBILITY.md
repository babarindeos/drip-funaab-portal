# Conference grant: eligibility to apply

Apply the earlier conference steps first (this builds on Steps 4B, 4C, 5 and 6; the files
here already contain their changes). Copy each file to the same relative path, then:

    php artisan conference:migrate      <- adds two columns (safe to run again)
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear

and hard-refresh the browser (Ctrl+F5). No `npm` step.

## The rules
To apply for a conference or workshop grant a staff member must:
1. give the date of their FUNAAB appointment (the number of years in FUNAAB is worked out from it);
2. have MORE than two years of service: two years or less is not qualified;
3. say whether they are a confirmed member of staff. "No" is not qualified. "Yes" needs the date
   of confirmation, and the letter of confirmation is uploaded with the other documents.

## How it works for the applicant
- A new application opens with ONE card: **Eligibility requirements**. The rest of the form stays
  hidden until they answer it and qualify.
- **Qualified**: a green message; the rest of the form opens (progress, rules check, all sections).
- **Not qualified**: a red message lists the reason(s). The form stays closed, the Documents
  button does not appear, and typing the Documents address sends them back. Nothing can be submitted.
- The date of confirmation must not be in the future or before the date of appointment.
- "Number of years in FUNAAB" is shown but worked out from the date, so it cannot be misstated.
  It counts COMPLETED years as of today: someone with 2 years 11 months has 2 and does not
  qualify until the third anniversary.
- "Date of FUNAAB appointment" and "No. of years in FUNAAB" ARE the form's existing "Date of 1st
  appointment" and "Years spent in the institution" (items 5 and 7), now asked up front. They still
  print on the nomination PDF in the same places, which is otherwise unchanged.
- The required **Letter of Confirmation of Appointment** appears on the Documents page for
  confirmed staff, and the submit checklist asks for it.
- The rules check on the form, the submit check and the admin review page all include the two
  new rules. Admin review and the applicant's own page show "Confirmed staff" and the date of confirmation.
- The call's details page lists both rules up front.

## Behaviour to know about
- The minimum is set in config/conference.php as `min_service_years` (3 = "more than two years").
  Change that one number to move the line.
- Applications already SUBMITTED are not judged by the new rules.
- Drafts that already exist must answer the eligibility card before they can continue (the rest of
  their answers are kept).
- If someone later changes their answers so they no longer qualify, the form closes again; what
  they had filled in is kept, not erased.
- The eligibility answers are saved on their own, apart from the main form, so they can never
  overwrite the rest of a draft.

## Files
NEW
- database/migrations/conference/2026_10_18_000007_add_eligibility_fields_to_conference_applications_table.php

REPLACE
- config/conference.php, routes/conference.php
- app/Models/Conference/ConferenceApplication.php
- app/Services/Conference/ConferenceEligibility.php
- app/Http/Controllers/Conference/Staff/ConferenceApplicationController.php
- resources/views/conference/staff/applications/edit.blade.php
- resources/views/conference/staff/applications/show.blade.php
- resources/views/conference/admin/applications/show.blade.php
- resources/views/conference/staff/calls/show.blade.php
- public/css/drip-theme.css

## Test (as staff, with an open call)
1. Press Apply on a call: you see only the Eligibility card.
2. Appointment date 1 year ago, "Yes", a confirmation date: red "not qualified" (service); the form stays closed.
3. Appointment date 5 years ago, "No": red "not qualified" (not confirmed).
4. "Yes" with a confirmation date before the appointment, or in the future: refused with a message.
5. Appointment 5 years ago, "Yes", valid confirmation date: green; the rest of the form opens.
6. Fill the form, open Documents and submission: "Letter of Confirmation of Appointment" is listed
   as required, and Submit stays disabled until it is uploaded.
7. Change the appointment date to 1 year ago and save the eligibility card: the form closes
   again, the Documents button disappears.
8. As admin, open a submitted application: confirmed staff and confirmation date are shown, and
   the eligibility check lists both rules.
