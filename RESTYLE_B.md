# Restyle, batch B: the staff side and the dashboards

No migration. Copy each file to the same relative path, then:

    php artisan view:clear
    php artisan route:clear

and hard-refresh the browser (Ctrl+F5). No `npm` step.

Needs batch A (the <x-flash> component and the call status filter) and the Conference
module's <x-form-field> component. You already have both.

## Pages restyled
**Admin dashboard**
- Four figures: calls open now, awaiting your decision, reviews outstanding, reviewers.
- A card for Call for Proposals and one for Conference Grants, each with its own figures
  and shortcuts.
- "Waiting for a decision": the oldest proposals and conference applications first.
- The old dashboard counted documents, workflows, departments and offices. Those belong to
  the larger system your project does not use, and a database without those tables would
  make the page fail. They are gone; the figures are now the portal's own.
- The conference part is optional: if the conference database is not reachable, the
  dashboard still opens and says so.

**Staff dashboard**
- Calls open now, and how many proposal and conference applications you have.
- "Open for applications": the next calls to close, for both modules, with Apply / Details.
- "My applications": your latest proposals and conference applications with their status.
- A new user is still sent to create their profile first.

**Staff call list (Call for Proposals)**
- Table with the deadline and "days left", the call's status, YOUR application's status
  (or "Not started"), and Apply / View application. Search is not needed here; it paginates.

**Proposal application form**
- The call's details first, then the form in two sections (details and documents).
- The same fields and the same rules as before. The guidance on which file holds the names
  (title page) and which must not (proposal file) is kept as help text.
- A confirmation before submitting, since it cannot be changed afterwards.
- A closed or not-yet-open call shows a message instead of the form.
- After applying, the page shows "Your application": status, details, document links.
- If a field is wrong the page reloads with the messages; the browser cannot keep chosen
  files, so they have to be chosen again (the page says so).

## Files
REPLACE
- app/Http/Controllers/Admin/Admin_DashboardController.php
- app/Http/Controllers/Staff/Staff_DashboardController.php
- app/Http/Controllers/Staff/Staff_ProposalApplicationController.php   (the list now paginates)
- resources/views/admin/dashboard.blade.php
- resources/views/staff/dashboard.blade.php
- resources/views/staff/proposals/index.blade.php
- resources/views/staff/proposals/application.blade.php
- public/css/drip-theme.css

## Test
1. Admin: Dashboard shows the figures, the two module cards and the waiting lists; every
   link works. Stop the conference database (or rename CONF_DB_DATABASE) to see the page still opens.
2. Staff: Dashboard shows open calls and your applications; links open the right pages.
3. Staff: Call for Proposals list: statuses, days left, Apply; paginates with more than 10 calls.
4. Apply: submit with a missing file or a wrong type: messages appear, choose files again;
   submit properly: "Your application" appears with working document links.
5. Open a closed call: a message instead of the form. Open one you already applied to: your application.
