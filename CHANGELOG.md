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

- The test suite always uses the in-memory SQLite database, even when `DB_*` environment variables are set. Previously, running the tests inside the Docker container wiped the development database.

### Removed

- The unused `.env.docker` file.
- The `8443` port mapping from Docker Compose (HTTPS is disabled in the development Caddyfile).
