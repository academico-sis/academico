# About this project
<!-- ALL-CONTRIBUTORS-BADGE:START - Do not remove or modify this section -->
[![All Contributors](https://img.shields.io/badge/all_contributors-1-orange.svg?style=flat-square)](#contributors-)
<!-- ALL-CONTRIBUTORS-BADGE:END -->
<!-- ALL-CONTRIBUTORS-BADGE:START - Do not remove or modify this section -->
[![All Contributors](https://img.shields.io/badge/all_contributors-13-orange.svg?style=flat-square)](#contributors-)
<!-- ALL-CONTRIBUTORS-BADGE:END -->

Academico is an open-source, Lavarel-based school management platform. Its main features include course management, enrolments management, resources scheduling, reports and stats. It is primarily targeted at small and medium-sized institutions who need a simple and affordable solution to manage their school and courses.

# New 2026 version (Filament-based)
The first versions of this project were built with the awesome Backpack for Laravel framework. However, the application was entirely rewritten with Laravel Filament. No changes in the database structure has been made, so the Filament version should work as a drop-in replacement of the Backpack version, with similar features. You can still access the Backpack version in the `pro` branch (but a Backpack license is required and the packages have not been updated in a long time). I strongly recommend switching to the Filament version.

# Disclaimer
The Filament version is still work in progress. Please use with caution, and report bugs if you encounter them. Contributions are welcome to make Academico a better, more usable software.

# Contributors welcome! ✨
If you are using this applications and want to make some improvements for your own needs, or just willing to contribute to an open-source project, feel free to open an issue and make some suggestions. Contributions might go from writing code, to extending the documentation or the translations, or simply suggesting new features, UX improvements, and so on.

Thanks goes to these wonderful people for past or current version of this application ([emoji key](https://allcontributors.org/docs/en/emoji-key)):

<!-- ALL-CONTRIBUTORS-LIST:START - Do not remove or modify this section -->
<!-- prettier-ignore-start -->
<!-- markdownlint-disable -->
<table>
  <tbody>
    <tr>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/rafaelfariasbsb"><img src="https://avatars.githubusercontent.com/u/4338815?v=4?s=100" width="100px;" alt="Rafael Farias"/><br /><sub><b>Rafael Farias</b></sub></a><br /><a href="#translation-rafaelfariasbsb" title="Translation">🌍</a></td>
    </tr>
  </tbody>
</table>

<!-- markdownlint-restore -->
<!-- prettier-ignore-end -->

<!-- ALL-CONTRIBUTORS-LIST:END -->

This project follows the [all-contributors](https://github.com/all-contributors/all-contributors) specification. Contributions of any kind welcome!


# Getting started

This application runs in Docker using FrankenPHP (a modern PHP application server built on Caddy) and MariaDB.

## Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (includes Docker Compose)

## Quick Start

1. Clone the repository and copy the environment file:

```bash
git clone https://github.com/academico-sis/academico.git
cd academico
cp .env.example .env
```

2. Start the containers (the first run builds the image, which may take a few minutes):

```bash
docker compose up -d
```

This starts two services:
- **app** — the FrankenPHP application server on `http://localhost:8080`
- **mariadb** — a MariaDB 11 database server on port `3306`

The app container waits for MariaDB to be healthy before starting.

3. Generate the application key:

```bash
docker compose exec app php artisan key:generate
```

4. Run the database migrations:

```bash
docker compose exec app php artisan migrate
```

5. (Optional) Seed the database with sample data:

```bash
docker compose exec app php artisan db:seed
```

6. Open `http://localhost:8080` in your browser.

## Running commands inside the container

All PHP and artisan commands should be run inside the `app` container:

```bash
# Run tests
docker compose exec app php artisan test

# Run the linter
docker compose exec app ./vendor/bin/pint

# Run any artisan command
docker compose exec app php artisan <command>
```

## Stopping the environment

```bash
docker compose down          # Stop containers (data is preserved in a Docker volume)
docker compose down -v       # Stop containers and delete the database volume
```

## Custom Login Page

The Docker setup supports an optional custom login page via a volume mount in `docker-compose.yml`:

```yaml
- /path/to/custom-views/login.blade.php:/app/resources/views/filament/auth/login.blade.php
```

The custom login class (`App\Filament\Auth\Login`) detects at runtime whether this Blade file is present. When the file exists, it renders the custom layout; otherwise, the standard Filament login page is shown.

## Contributors ✨

Thanks goes to these wonderful people ([emoji key](https://allcontributors.org/docs/en/emoji-key)):

<!-- ALL-CONTRIBUTORS-LIST:START - Do not remove or modify this section -->
<!-- prettier-ignore-start -->
<!-- markdownlint-disable -->
<!-- markdownlint-restore -->
<!-- prettier-ignore-end -->
<!-- ALL-CONTRIBUTORS-LIST:END -->

This project follows the [all-contributors](https://github.com/all-contributors/all-contributors) specification. Contributions of any kind welcome!