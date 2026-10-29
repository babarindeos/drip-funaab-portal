# Restyle, batch C: public pages, sign in, password reset and the staff profile

No migration (password reset uses the `password_reset_tokens` table you already have).
The files are already in place. Run:

    php artisan view:clear
    php artisan route:clear
    php artisan config:clear

and hard-refresh the browser (Ctrl+F5). No `npm` step.

## Pages restyled
**Public**
- **Homepage**: the latest calls as cards (status, opening and closing dates, days left, advert link)
  beside a proper sign-in card with "Keep me signed in", **Forgot password?**, "Create an account"
  and a link to the administrator sign-in. On a phone, sign-in comes first.
- **Calls for Proposals** (public list): the same call cards, with a count and paging.
- **Register**: a centred card with clear labels. Middle name is now optional (it was required on the
  page but never by the server). Clearer messages, e.g. when an email already has an account.
- **Administrator sign-in**: same design as the staff sign-in.
- The public header shows only "Sign in" on small phones, so it no longer wraps.

**Password reset (new)**
- "Forgot password?" on both sign-in pages opens a page that emails a link (valid 60 minutes, usable once).
- The link opens "Choose a new password" (at least 8 characters). Afterwards staff go to the homepage
  sign-in, admins to the admin sign-in, with a "password changed" message.
- The email uses the portal's email style and builds its link on DRIP_BASE_URL, like the other emails.
- The answer is the same whether or not an address has an account, so nobody can use the page to find
  out who is registered. If the mail server cannot be reached, the page says so instead of claiming it sent.
- Asking twice within a minute is refused politely; requests are limited to 6 a minute.

**Staff profile**
- **First-time profile** (where a new user is sent): now inside the staff layout, with a welcome,
  designation, phone and an optional photo. Saving takes you to the dashboard.
- **My profile**: photo card (with "Change photo"), personal details, and a link to change the password.
- **Edit details**: designation, phone and photo; name and email shown read-only.
- **Change password**: real password boxes (they were plain text before, so passwords showed on screen).
- **My signature** and **someone else's profile** (both used by the old document workflow) restyled and
  made safe when a record is missing (they used to crash).
- Tabs across My profile / Edit details / Change password.

## Fixes made along the way
- **Uploads are safe now.** Profile photos had NO checks and kept the browser's file name, so a `.php`
  file could be uploaded into the public folder. Photos must now be real JPG/PNG images (2 MB, at least
  100 x 100); signatures real PNGs (1 MB). Files are saved under a name the portal chooses, with the
  extension of the real file type, and the old file is removed when replaced.
- **Login attempts are limited**: 5 wrong passwords for one email from one address locks it for a minute
  (staff and admin). The password is no longer sent back into the form after a failed sign-in.
- "Keep me signed in" now works. Signing out fully ends the session.
- My profile, Edit and Change password no longer crash for a user without a profile; they go to the
  first-time profile page instead.
- Phone numbers are checked (digits, spaces, + ( ) - only). Password changes need 8+ characters and must
  differ from the current one.
- The "Profile created" debugging stop (`dd`) that would show an error page is gone.
- Archived calls are no longer listed on the public pages (same as the Conference module).
- The missing `use` line for `Admin_MinistryController` was added to routes/web.php, so the Ministries
  pages open and `php artisan route:list` / `route:cache` work again.
- Admin_AuthController had a commented-out block with an admin's email and password; it was removed.
  **If that password is real, change it.**

## Files
NEW
- app/Mail/PasswordResetMail.php
- resources/views/emails/password-reset.blade.php
- resources/views/components/pager.blade.php            paging used by the public lists
- resources/views/components/public-call.blade.php      one call card on the public pages
- resources/views/components/profile-photo.blade.php    photo or initials
- resources/views/staff/profile/_head.blade.php         breadcrumb, title and tabs of the profile pages

REPLACE
- routes/web.php                                        password reset routes, Ministry import
- app/Models/User.php                                   sends the reset email
- app/Http/Controllers/Auth/PasswordResetLinkController.php
- app/Http/Controllers/Auth/NewPasswordController.php
- app/Http/Controllers/Staff/Staff_AuthController.php
- app/Http/Controllers/Admin/Admin_AuthController.php
- app/Http/Controllers/Staff/Staff_ProfileController.php
- app/Http/Controllers/Guest/Guest_WelcomeController.php
- resources/views/{welcome,register,call_for_proposals}.blade.php
- resources/views/admin/auth/login.blade.php
- resources/views/auth/{forgot-password,reset-password}.blade.php
- resources/views/staff/profile/{create,myprofile,edit,change_password,my_signature,user_profile}.blade.php
- resources/views/components/icon.blade.php             a few new icons
- public/css/drip-theme.css                             batch C styles at the end

## Test
1. Homepage: call cards and the sign-in card; on a phone, sign-in is on top.
2. Sign in with a wrong password: message, the password box is empty. Six wrong tries: "Too many attempts".
3. Register with empty fields: messages under each box. Register properly: the password email arrives.
4. Sign in as the new user: the first-time profile page. Try a .php or a tiny image as the photo: refused.
   Save with a real photo: you land on the dashboard and the photo is in the header.
5. My profile: change photo; Edit details (try a bad phone number); Change password (wrong current
   password is refused; then change it and sign in with the new one).
6. Sign out, "Forgot password?": the email arrives (Mailtrap); the link opens the new-password page;
   set it and sign in. Open the same link again: "expired or already used".
7. Admin sign-in page: same design; "Forgot password?" works for an admin too and returns to /admin.
8. Public Calls for Proposals list: an archived call is not shown.

---

# Addendum: IBR and Conference/Workshop calls on the public pages

No migration. Run `php artisan view:clear` and `php artisan route:clear`, then Ctrl+F5.

## What changed
- **Two public links** in the header and on the homepage: **Call for IBR Proposals** and
  **Call for Conference/Workshop**. Each opens a list of only that kind of call.
- **New public page**: Call for Conference/Workshop (`/conference_calls`). It lists the published
  conference calls (drafts and archived ones are hidden) with who may apply (teaching / non-teaching
  staff), the earliest event date and the maximum number of days. If the conference database cannot
  be reached, the page still opens and says the calls cannot be shown right now.
- The old public list is now titled **Call for IBR Proposals** (same address, `/call_for_proposals`).
  Each list links to the other.
- **Homepage**: the heading reads "Latest calls for Research Proposals and Conferences/Workshops". It shows the
  6 most recent calls of both kinds, newest first, and **every card is labelled** IBR Proposal (green)
  or Conference/Workshop (blue). The homepage no longer pages; the two lists do.
- On phones the header links sit on their own row under the logo.

## Files
NEW
- resources/views/conference_calls.blade.php

REPLACE
- routes/web.php                                    the /conference_calls route
- app/Http/Controllers/Guest/Guest_WelcomeController.php
- resources/views/welcome.blade.php
- resources/views/call_for_proposals.blade.php
- resources/views/components/public-call.blade.php  labels each card with its kind
- resources/views/partials/_header_guest.blade.php
- public/css/drip-theme.css
- (small wording/spacing touch-ups) resources/views/register.blade.php,
  resources/views/staff/profile/{myprofile,change_password}.blade.php

## Test
1. Homepage: the heading, the two buttons, and a label on every card. A draft or archived call of
   either kind is not shown.
2. Header "Call for IBR Proposals": only IBR calls. "Call for Conference/Workshop": only conference calls.
3. Stop MySQL's conference database (or rename CONF_DB_DATABASE): the homepage still opens with the IBR
   calls, and the Conference/Workshop page says the calls cannot be shown right now.
