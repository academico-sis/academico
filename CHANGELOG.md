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

### Removed

- The unused `.env.docker` file.
- The `8443` port mapping from Docker Compose (HTTPS is disabled in the development Caddyfile).
