# Restyle, batch A: the admin side of Call for Proposals

No migration. Copy each file to the same relative path, then:

    php artisan view:clear
    php artisan route:clear

and hard-refresh the browser (Ctrl+F5). No `npm` step.

Needs the Conference module's `resources/views/components/form-field.blade.php`
(you already have it from Step 3A) and the earlier theme files.

## Pages restyled
Admin -> Call for Proposals
- **Call list**: summary strip (calls, open now, applications), search, status
  filter, pagination, status pills, Applications / Results / Edit / Delete.
- **New call / Edit call**: sectioned forms; after saving you go to the list
  (before, you stayed on the form).
- **Call details**: a real read-only page (before, it was a copy of the create
  form whose Save button did nothing), with figures and shortcuts.
- **Results**: ranking with summary figures, PDF and Excel (CSV) export.
- **Send to reviewer**: the proposal, a simple reviewer dropdown (reviewers who
  already have it are left out), and the list of reviewers with their link,
  Copy, Resend and Remove.
- **Application decision page**: proposal details, documents, the decision
  form (status, remark, "include in email"), and a reviews summary.
- **Reviewers**: list with search, add, EDIT and DELETE.
- **Analytics**: the per-call figures in the new design.

## Fixes made along the way
- **Reviewers could not be edited or deleted**: the Edit and Delete links pointed
  at controller methods that did not exist. They now work. A reviewer who has been
  sent proposals cannot be deleted (it would break the review records).
- A reviewer's email must now be unique, with a clear message (before, a duplicate
  gave a generic error).
- Call adverts are now checked: JPG, PNG or PDF, up to 10 MB (before, any file was accepted).
- Saving a call or a reviewer now returns to the list with a message.
- The old reviewer search box (a jQuery suggestion list) and the "generate uuid"
  utility button are gone from the pages. Their routes still exist.
- One message component (<x-flash>) shows every kind of message the older
  controllers send, so notices appear consistently.

## Files
NEW
- resources/views/components/flash.blade.php
- resources/views/admin/call_for_proposals/_form.blade.php
- resources/views/admin/reviewers/edit.blade.php
- resources/views/admin/reviewers/_form.blade.php

REPLACE
- app/Models/CallForProposal.php            status filter for the list
- app/Models/Reviewer.php                   how many proposals each reviewer was sent
- app/Http/Controllers/Admin/Admin_CallForProposalController.php
- app/Http/Controllers/Admin/Admin_ReviewerController.php
- resources/views/admin/call_for_proposals/{index,create,edit,show,results,proposal_application}.blade.php
- resources/views/admin/proposal_reviewers/send_to_reviewer.blade.php
- resources/views/admin/reviewers/{index,create}.blade.php
- resources/views/admin/analytics/index.blade.php
- public/css/drip-theme.css

## Test (as admin)
1. Call for Proposals: summary figures; search; status filter; create a call with an
   advert (try a .docx: refused), then edit it; Delete a call with no applications.
2. Open a call's details page: figures and buttons work.
3. Applications -> Results: ranking, exports; Decide opens the decision page.
4. Decision page: change the status with the "include remark" box ticked and unticked;
   the applicant's email follows the rules from Module 9.
5. Send to reviewer: pick a reviewer, send; the list shows the link, Copy works,
   Resend works, Remove works (and is locked after the review is submitted).
6. Reviewers: add one, edit it, try a duplicate email, delete one that has no proposals,
   and check one that has proposals cannot be deleted.
7. Analytics: figures match the Results pages.
