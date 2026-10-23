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
- Scheduled tasks that depend on a setting (monthly report, external courses report, partnership alerts) are always registered and check the setting when they run, so changing it takes effect without restarting the scheduler.

### Fixed

- The seeded attendance types now have a color, so each series of the attendance reports is displayed in a different color instead of the grey fallback.
- The taught and sold hours of the reports counted 0 hours for courses without a remote volume (or without an in-person volume).
- Creating a teacher or a student with the same first and last name as an existing user failed with a server error. Homonyms now get a numbered username.
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

### Security

- Upgraded Filament from 5.2 to 5.10.2, which fixes several security advisories (XSS in image columns and summarizers, unauthenticated uploads on auth pages, attach/associate scope, multi-factor authentication). The custom login page now relies on Filament's authentication, so it gets the timing-safe login and no longer reveals whether the password of an account without panel access is valid.
- Users who can view courses, events, periods, partners, external courses, enrollments, students, teachers or leaves can no longer create, edit or delete them without the matching permission (`courses.edit`, `courses.delete`, `enrollments.edit`, `enrollments.delete`, `hr.manage`). ([GHSA-w76j-4qgr-3r9f](https://github.com/academico-sis/academico/security/advisories/GHSA-w76j-4qgr-3r9f))
- The resources of the Settings cluster are restricted to administrators: they were reachable by URL by any user with panel access, including teachers.
- The grade and skill evaluation pages check that the enrollment belongs to the course before saving grades, results, comments or skill evaluations, and their course and read-only state can no longer be changed by the client. ([GHSA-x47v-xg6f-42xj](https://github.com/academico-sis/academico/security/advisories/GHSA-x47v-xg6f-42xj))

### Removed

- The unused `.env.docker` file.
- The `8443` port mapping from Docker Compose (HTTPS is disabled in the development Caddyfile).
- The Ecuasolutions accounting integration and the invoicing drivers (`INVOICING_SYSTEM`, `ECUASOLUTIONS_*`), with the "send to accounting" option of the checkout and its retry action. Receipt numbers can still be entered at checkout when invoice numbering is manual.
- The MailerLite integration (`EXTERNAL_MAILING_ENABLED`, `MAILING_SYSTEM`, `MAILERLITE_API_KEY`, `*_MAILING_LIST_ID`) and the `mailerlite/mailerlite-api-v2-php-sdk` package.
- The enrollment sheet (`ENROLLMENT_SHEET_STYLE`), which was only implemented for one school, and the `phpoffice/phpword` package.
- The school-specific certificate templates (`CERTIFICATES_STYLE`, `CERTIFICATES_SIG_*`). They were not reachable from the interface.
- The hook for a custom login view.
- The unused `books_module`, `admin_email`, `allowed_blank_fields`, `app_url`, `sentry_laravel_dsn` and `languages` keys of `config/academico.php`, with the `BOOKS_MODULE_ENABLED`, `ADMIN_EMAIL` and `ALLOWED_EMPTY_INVOICE_FIELDS` variables.
