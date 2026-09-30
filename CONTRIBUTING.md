# Contributing to Academico

Thank you for your interest in Academico! Contributions of any kind are welcome: code, documentation, translations, bug reports and feature ideas.

## Reporting bugs and suggesting features

- Search the [existing issues](https://github.com/academico-sis/academico/issues) first.
- Open a new issue using the bug report or feature request form.
- Security problems must **not** be reported in a public issue: see [SECURITY.md](SECURITY.md).

## Setting up a development environment

Docker is the only requirement. PHP, Composer and Node all run inside containers.

```bash
git clone https://github.com/academico-sis/academico.git
cd academico
docker compose up -d --wait
```

The first start takes a few minutes. See the [README](README.md#getting-started) for details, including how to load sample data.

Your checkout is mounted into the containers, so changes to PHP and Blade files are visible immediately. Frontend assets (`resources/css`, `resources/js`) are rebuilt automatically by the `assets` container.

## Making a change

1. Fork the repository and create a branch from `main`.
2. Make your change. Keep features generic and configurable rather than specific to one school.
3. Add or update tests for the behaviour you changed.
4. Run the test suite and the code style fixer:

```bash
docker compose exec app php artisan test
docker compose exec app ./vendor/bin/pint
```

5. Add a line to the `Unreleased` section of [CHANGELOG.md](CHANGELOG.md) if the change is visible to users or to people hosting the application.
6. Open a pull request against `main` and describe what the change does and why.

Every pull request runs the tests, the code style check and a production image build. All three must pass before a pull request is merged.

## Conventions

- Code style follows [Laravel Pint](https://laravel.com/docs/pint) with its default preset.
- Tests use PHPUnit and run against an in-memory SQLite database.
- User-facing text must be translatable: add strings to the files in `lang/`.
- Database changes need a migration. The schema must stay compatible with existing installations.

## Translations

Translation files live in `lang/`. To improve a translation or add a language, edit or add the corresponding files and open a pull request.

## Recognition

Contributors are listed in the README following the [all-contributors](https://github.com/all-contributors/all-contributors) specification.

## License

By contributing, you agree that your contributions are licensed under the [MIT License](LICENSE).
