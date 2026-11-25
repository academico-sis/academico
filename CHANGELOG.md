# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

- Development Docker image and entrypoint: `docker compose up` now installs PHP and Node dependencies, creates `.env`, generates the application key and runs the migrations. Nothing but Docker is needed on the host.
- `assets` Compose service that builds the frontend assets and rebuilds them on change.
- `APP_PORT` and `FORWARD_DB_PORT` variables to change the ports published by Docker Compose.
- Continuous integration: tests, code style check and production image build on every pull request.
- Versioned Docker images are published when a `v*` tag is pushed.
- `LICENSE`, `SECURITY.md`, `CONTRIBUTING.md`, issue forms and a pull request template.
- A "General settings" page in the Settings area to edit the institution settings: name, address, contact details and logo, language, timezone, currency, notifications, features, registration and invoicing. The logo is shown in the panel, on the student pages and on invoices.
- Certificates, result sheets and course results as PDF documents, in the institution language. They can be downloaded from the enrollment, course and grades pages and from the teacher dashboard. Certificates are disabled by default; their title, text (with placeholders such as `{student}`, `{course}` and `{hours}`), place, signer, signature and background image are set in the Certificates tab of the settings.
- **Multi-tenancy:** one install can serve several institutions, each with its own database, files and domains, using `spatie/laravel-multitenancy`. The institution is found from the host name of the request, and its settings, language, URL, files, cache and permissions are switched accordingly. Queued jobs run for the institution that queued them. Host names that match no institution get a 404. A single-institution install keeps one database and works as before: a migration adds the `tenants` and `tenant_domains` tables to it and registers it as its only tenant, which answers on any host name. To serve several institutions, set `LANDLORD_DB_DATABASE` to a separate landlord database (see `.env.example`).
- Commands to run several institutions: `academico:tenant:create` creates an institution with its database (named after `TENANCY_DATABASE_PREFIX` and the institution), files folder, domain and first administrator, who receives a link to choose a password; `academico:tenant:domain` adds, makes primary or removes a domain; `academico:tenant:list` lists the institutions; `academico:migrate` runs the migrations of the landlord database and of every institution after an upgrade. The README explains the setup.
- A MariaDB job in continuous integration: it runs the migrations (and rolls back the latest ones) for a single institution, creates two institutions with the tenant commands, runs the queue worker, and checks that web requests reach the institution of their host name.
- The README shows how to run several institutions behind Traefik: one web container, one scheduler and one worker for all institutions, with the routes of their domains in a Traefik file provider configuration.
- Error reports sent to Sentry carry the ID of the institution.
- The login page can list demo accounts, with a button that fills the form. The sample data (`TestSeeder`) lists an administrator, a secretary, a teacher and a student (password `secret`), and names the institution after its tenant in a multi-tenant install. Other installs list no account.
- On a demo install, the login page warns visitors that what they enter is public and must not include real names or email addresses. With `DEMO_MAILBOX_URL` set, it also links the public mailbox where the emails of the demo are caught (such as Mailpit).

### Changed

- Upgraded `spatie/laravel-activitylog` to v5. A migration moves the tracked changes of existing activities from `properties` to the new `attribute_changes` column and drops the unused `batch_uuid` column. The `ACTIVITY_LOGGER_ENABLED` variable is still honoured, `ACTIVITYLOG_ENABLED` takes precedence.
- Upgraded `spatie/laravel-permission` to v8, `laravel/tinker` to v3, `phpunit/phpunit` to v13 and `concurrently` to v10 (requires Node.js 22).
- The test suite always uses the in-memory SQLite database, even when `DB_*` environment variables are set. Previously, running the tests inside the Docker container wiped the development database.
- Course changes now only apply to the class sessions from a date chosen in the course form (today by default): earlier sessions keep their date, time, teacher and room, and sessions with recorded attendance are never deleted. Sessions given to a substitute, moved to another room or renamed keep these changes. A warning lists the sessions left outside the course dates.
- A class session on a day the teacher is on leave is now created without teacher (waiting for a substitute) instead of not being created.
- `academico:resync-coursetimes` creates the missing sessions and deletes those outside the schedule, but no longer deletes sessions with attendance or sessions added by hand.
- **Institution settings are no longer read from `.env`.** They are stored in the database and edited in Settings > General settings, so after upgrading, enter them again there. This covers the `COMPANY_*`, `CURRENCY_*`, `SEND_EMAILS_FOR_*`, `INCLUDE_TAKINGS_IN_REPORTS`, `MANAGER_EMAIL`, `REPORTS_EMAIL`, `SECRETARY_EMAIL`, `COURSES_TIMEZONE`, `PARTNERSHIP_ALERTS`, `EXTERNAL_COURSES_*`, `MONTHLY_REPORT`, `TEACHERS_CAN_*`, `ATTENDANCE_REPORTS_ENABLED`, `REGISTRATION_PICTURE_*`, `ENSURE_EMAIL_UNICITY`, `ACCOUNTING_ENABLED`, `ALLOW_SCHEDULED_PAYMENTS`, `INVOICE_NUMBERING`, `PRICE_CATEGORIES_ENABLED` and `MEMBERS_ENABLED` variables. The institution name is also used as application name and mail sender name.
- The institution language is set in the settings (English by default) and no longer follows `APP_LOCALE`. It is used by queued jobs and scheduled tasks, by the documents, and for visitors whose browser asks for no supported language (they previously got the first supported language).
- The `config` table (current and enrollment periods, report start periods, invoice footer) is moved into the new `settings` table by a migration.
- Scheduled tasks that depend on a setting (monthly report, external courses report) are always registered and check the setting when they run, so changing it takes effect without restarting the scheduler.
- **Email addresses are unique, and usernames are removed.** Users log in with their email address. The registration, student and teacher forms refuse an address that another user already has, and the "Refuse an email address that is already registered" setting is removed. Students can still be created without an email address. A migration adds a unique index on `users.email` and drops `users.username`. It stops if several users share an email address: run `php artisan academico:deduplicate-emails` to list them, then change them, or run the command again with `--fix` to keep each address on its oldest account and change the others to `name+ID@domain`. Then run the migrations again.
- The grade and skill evaluation buttons of the enrollment page are shown to users who can edit the grades of the course (`evaluation.edit`), instead of to administrators only.
- **Scheduled tasks run at their time in the institution timezone** (the Timezone setting) instead of UTC: the period check and the monthly report at midnight, the external courses report at 03:15 (previously 02:10 UTC), the report cache at 05:15 and the attendance reminders at 08:15. They are Artisan commands that can also be run by hand: `academico:check-periods`, `academico:monthly-report`, `academico:external-courses-report`, `academico:build-report` and `academico:attendance-reminders`. The scheduler still needs `php artisan schedule:run` every minute. The external courses report also uses the date in the institution timezone to find the partners whose report day is today.
- Private files (student photos, certificate signatures) are served under `/private/` instead of `/storage/`. Files of the public disk that are not under the `public/storage` link, such as those of other institutions, are served by the application.
- Scheduled tasks run for each institution, at its time and in its timezone. A failure for one institution is reported and the others still run. Each task accepts `--tenant` to run for some institutions only.
- The database queue, job batches, failed jobs and the database cache use the landlord connection, shared by all institutions (`DB_QUEUE_CONNECTION` and `DB_CACHE_CONNECTION` can still change it). For a single institution, this is the same database as before. Queued jobs are now dispatched once the database transaction that queued them is committed.

### Fixed

- The development container trusts the bind-mounted checkout, so startup no longer reports a fatal Git "dubious ownership" error.
- The seeded attendance types now have a color, so each series of the attendance reports is displayed in a different color instead of the grey fallback.
- The taught and sold hours of the reports counted 0 hours for courses without a remote volume (or without an in-person volume).
- Creating a teacher or a student with the same first and last name as an existing user failed with a server error.
- Enrolling a student, from the student or from the course, now enrolls them in the submodules of the course, and refuses duplicate enrollments and courses without spots left. Changing the course of an enrollment also refuses full courses. Enrollments no longer store the course price, so they follow the student's price category.
- A course can no longer end before it starts, and a time slot can no longer end before it begins: such courses were saved without any class.
- Deleting a course that has enrollments failed with a server error. The delete button is now disabled with an explanation, and the bulk delete skips these courses.
- Birthdates in the future and negative weekly hours for teachers are refused.
- Saving a course, even without any change, deleted and re-created all its class sessions, with their attendance. Changing the course dates or weekly schedule did the same, and the re-created sessions could keep the previous dates.
- Changing the teacher, room or name of a course rewrote every session of the course, including past sessions (whose hours moved to the new teacher) and sessions given to a substitute.
- Assigning a teacher on leave to a class session failed with a server error. The event form now refuses it.
- An existing leave could not be edited, and deleting or moving a leave did not give the sessions of that day back to the teacher.
- The sample data (`TestSeeder`) created class sessions that did not match the course schedules.
- Invoices printed hard-coded Spanish labels for the course and the customer details, and the bulk invoice download email was only in French. Both are now translated.
- Teachers could not open the attendance pages of their own classes and courses, nor the skill evaluation page of their own courses, and nothing linked to these pages. The teacher dashboard now links each pending class to its attendance page, and each course to its attendance, grades and skills pages. Attendance can only be recorded for students enrolled in the course.
- Invoices were printed in the language of the user who downloaded them. They are now printed in the institution language, like certificates. The month of the monthly report is also named in the institution language.
- Password reset links could not be sent, because the table holding the reset tokens had been renamed back to its former name. A migration restores the `password_reset_tokens` table.
- Teachers were reminded of their classes without attendance only when the absence emails to students were turned on. The reminders have their own setting in the Notifications tab of the settings, which a migration turns on where the absence emails were on.
- Failed queued jobs could not be recorded, because the `failed_jobs` table had no `uuid` column. A migration adds it.

### Security

- Upgraded Filament from 5.2 to 5.10.2, which fixes several security advisories (XSS in image columns and summarizers, unauthenticated uploads on auth pages, attach/associate scope, multi-factor authentication). The custom login page now relies on Filament's authentication, so it gets the timing-safe login and no longer reveals whether the password of an account without panel access is valid.
- Users who can view courses, events, periods, partners, external courses, enrollments, students, teachers or leaves can no longer create, edit or delete them without the matching permission (`courses.edit`, `courses.delete`, `enrollments.edit`, `enrollments.delete`, `hr.manage`). ([GHSA-w76j-4qgr-3r9f](https://github.com/academico-sis/academico/security/advisories/GHSA-w76j-4qgr-3r9f))
- The resources of the Settings cluster are restricted to administrators: they were reachable by URL by any user with panel access, including teachers.
- The grade and skill evaluation pages check that the enrollment belongs to the course before saving grades, results, comments or skill evaluations, and their course and read-only state can no longer be changed by the client. ([GHSA-x47v-xg6f-42xj](https://github.com/academico-sis/academico/security/advisories/GHSA-x47v-xg6f-42xj))
- The registration form only validated the step shown when it was submitted, and the browser could change that step, or turn off the mandatory picture and the acceptance of the terms and rules. It now validates every step on submission, and its steps and settings can no longer be changed from the browser.
- The student dashboard and account pages were open to every logged-in user, and saving the personal information there gave teachers and staff a student record, after which they were treated as students. These pages are now restricted to students, and the forced profile update can no longer be skipped from the browser.
- Passwords must have at least 8 characters (previously 6) at registration, in the student account and when resetting a password. Students must enter their current password to change it.
- Users who could view enrollments could also change their price, status and course, cancel them, attach or detach scholarships, and edit or delete any comment. These actions now require `enrollments.edit`. Comments can be added by users who can edit the enrollment or the student, and changed or deleted by their author or by users with the `comments.edit` permission (administrators by default). Setting the current or enrollment period requires `courses.edit`.
- The dashboard widgets were shown to every user of the panel, so teachers and viewers saw the enrollment and student figures and the classes of every teacher. The figures now require `enrollments.view`, the daily classes `calendars.view` and the period information `courses.view`.
- **Student photos are private.** They were stored on the public disk, where anyone could download them without logging in, at addresses built from sequential numbers. Photos are now stored on the private disk and shown through links that expire after 30 minutes. After upgrading, run `php artisan academico:move-student-photos` to move the existing photos to the private disk.
- **The `X-Forwarded-*` headers are only trusted from the proxies listed in the new `TRUSTED_PROXIES` variable.** They were trusted from any client, so anyone reaching the application directly could fake their IP address, for example to get around the registration rate limit. Behind a reverse proxy or load balancer, set `TRUSTED_PROXIES` to its addresses, or to `*` if the application cannot be reached without it, otherwise links may use HTTP instead of HTTPS.
- Session cookies are only sent over HTTPS when `APP_URL` starts with `https://`. `SESSION_SECURE_COOKIE` still overrides this.
- The production Docker image runs as `www-data` instead of root, and its base images are pinned to exact versions, which Dependabot now updates. Volumes mounted on `/app/storage` must be writable by `www-data` (UID 33). Local logs, sessions, caches and uploads are no longer copied into images built from a working copy.

### Removed

- The unused `.env.docker` file.
- The `8443` port mapping from Docker Compose (HTTPS is disabled in the development Caddyfile).
- The Ecuasolutions accounting integration and the invoicing drivers (`INVOICING_SYSTEM`, `ECUASOLUTIONS_*`), with the "send to accounting" option of the checkout and its retry action. Receipt numbers can still be entered at checkout when invoice numbering is manual.
- The MailerLite integration (`EXTERNAL_MAILING_ENABLED`, `MAILING_SYSTEM`, `MAILERLITE_API_KEY`, `*_MAILING_LIST_ID`) and the `mailerlite/mailerlite-api-v2-php-sdk` package.
- The enrollment sheet (`ENROLLMENT_SHEET_STYLE`), which was only implemented for one school, and the `phpoffice/phpword` package.
- The school-specific certificate templates (`CERTIFICATES_STYLE`, `CERTIFICATES_SIG_*`). They were not reachable from the interface.
- The hook for a custom login view.
- The unused `books_module`, `admin_email`, `allowed_blank_fields`, `app_url`, `sentry_laravel_dsn` and `languages` keys of `config/academico.php`, with the `BOOKS_MODULE_ENABLED`, `ADMIN_EMAIL` and `ALLOWED_EMPTY_INVOICE_FIELDS` variables.
- Partnership contracts: their start and end dates, tacit renewal, the expiry alerts and their setting, and the Partnerships report. Partners remain as the institutions of external courses, with the day their monthly report is sent. A migration drops the `started_on`, `expired_on`, `auto_renewal` and `last_alert_sent_at` columns of the `partners` table.
- The bulk invoice download (a zip archive sent by email), which nothing triggered.
- The unused `maatwebsite/excel` and `laravel/sail` packages.
- Unused code inherited from the Backpack version: events without listeners, traits and model methods.
- A migration drops unused tables (`lead_types`, `remote_events`, `schedule_presets`, `titles`) and columns (`lms_id` of `courses`, `levels`, `rhythms` and `users`, `courses.sync_to_lms`, `users.api_token`, `users.preferred_course_view`, `students.lead_type_id`, `students.how_did_you_know_us`, `enrollments.invoice_id` and `invoices.company_id`).
- The `leads.manage` and `student.edit` permissions, which nothing checked. A migration removes them; editing students still requires `enrollments.edit`.
