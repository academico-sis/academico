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

### Changed

- Upgraded `spatie/laravel-activitylog` to v5. A migration moves the tracked changes of existing activities from `properties` to the new `attribute_changes` column and drops the unused `batch_uuid` column. The `ACTIVITY_LOGGER_ENABLED` variable is still honoured, `ACTIVITYLOG_ENABLED` takes precedence.
- Upgraded `spatie/laravel-permission` to v8, `laravel/tinker` to v3, `phpunit/phpunit` to v13 and `concurrently` to v10 (requires Node.js 22).
- The test suite always uses the in-memory SQLite database, even when `DB_*` environment variables are set. Previously, running the tests inside the Docker container wiped the development database.

### Fixed

- The seeded attendance types now have a color, so each series of the attendance reports is displayed in a different color instead of the grey fallback.
- The taught and sold hours of the reports counted 0 hours for courses without a remote volume (or without an in-person volume).
- Creating a teacher or a student with the same first and last name as an existing user failed with a server error. Homonyms now get a numbered username.
- Enrolling a student, from the student or from the course, now enrolls them in the submodules of the course, and refuses duplicate enrollments and courses without spots left. Changing the course of an enrollment also refuses full courses. Enrollments no longer store the course price, so they follow the student's price category.
- A course can no longer end before it starts, and a time slot can no longer end before it begins: such courses were saved without any class.
- Deleting a course that has enrollments failed with a server error. The delete button is now disabled with an explanation, and the bulk delete skips these courses.
- Birthdates in the future and negative weekly hours for teachers are refused.

### Security

- Upgraded Filament from 5.2 to 5.10.2, which fixes several security advisories (XSS in image columns and summarizers, unauthenticated uploads on auth pages, attach/associate scope, multi-factor authentication). The custom login page now relies on Filament's authentication, so it gets the timing-safe login and no longer reveals whether the password of an account without panel access is valid.
- Users who can view courses, events, periods, partners, external courses, enrollments, students, teachers or leaves can no longer create, edit or delete them without the matching permission (`courses.edit`, `courses.delete`, `enrollments.edit`, `enrollments.delete`, `hr.manage`). ([GHSA-w76j-4qgr-3r9f](https://github.com/academico-sis/academico/security/advisories/GHSA-w76j-4qgr-3r9f))
- The resources of the Settings cluster are restricted to administrators: they were reachable by URL by any user with panel access, including teachers.
- The grade and skill evaluation pages check that the enrollment belongs to the course before saving grades, results, comments or skill evaluations, and their course and read-only state can no longer be changed by the client. ([GHSA-x47v-xg6f-42xj](https://github.com/academico-sis/academico/security/advisories/GHSA-x47v-xg6f-42xj))

### Removed

- The unused `.env.docker` file.
- The `8443` port mapping from Docker Compose (HTTPS is disabled in the development Caddyfile).
