# About this project
<!-- ALL-CONTRIBUTORS-BADGE:START - Do not remove or modify this section -->
[![All Contributors](https://img.shields.io/badge/all_contributors-1-orange.svg?style=flat-square)](#contributors-)
<!-- ALL-CONTRIBUTORS-BADGE:END -->
Academico is an open-source, Lavarel-based school management platform. Its main features include course management, enrolments management, resources scheduling, reports and stats. It is primarily targeted at small and medium-sized institutions who need a simple and affordable solution to manage their school and courses.

See overview and features at https://academico.site

Demo available at http://demo.academico.site (use `admin@academico.site` with password `secret`)

# Installation

## Requirements
See https://laravel.com/docs/6.x/installation#server-requirements

The following instructions assume that you are somehow familiar with Laravel.

## Download and install the code
* `git clone https://github.com/laxsmi/academico.git`
* Copy `.env.example` to `.env` and  edit the file with your database settings (you need to create a new database)
* `composer install`
* `php artisan key:generate`
* `php artisan migrate`
* `php artisan db:seed`

The last command will fill the database with the defaults you need to launch the application for the first time.

## Run the application
* `php artisan serve`

If all went well, you should be able to open the application in your browser at `http://127.0.0.1:8000`

Login using username `admin@academico.site` and password `secret`

## Set up in production
You need to install a webserver according to the Laravel documentation when you're ready to move the application to production. Some features will also require a queue worker.

# Documentation
* Technical documentation -> https://github.com/laxsmi/academico/wiki
* User documentation -> https://laxsmi.gitbook.io/academico-docs/

# Contributors needed!
This project was initially developed to address the needs of one specific school, however, the project was published as open-source with the hope that others might use it. Please feel free to try it or to request a demo to see if it could match with your expectations.

The initial developer is still actively maintaining the code and fixing bugs on his spare time, however, the project would greatly benefit from new contributors. Contributing to this project is also a great way to train your developer skills.

Please see [this page on the wiki](https://github.com/laxsmi/academico/wiki/Development-Roadmap) for details.

# License
This software is based on Laravel and Backpack for Laravel. You will need to [acquire a Backpack license](https://backpackforlaravel.com/pricing) if you decide to use it for _commercial_ use. This is the recommended solution and Backpack does provide a nice, clean and easy CRUD panels throughout the application. Backpack for Laravel is kind enough to issue free licences for non-commercial projects.

## Contributors ✨

Thanks goes to these wonderful people ([emoji key](https://allcontributors.org/docs/en/emoji-key)):

<!-- ALL-CONTRIBUTORS-LIST:START - Do not remove or modify this section -->
<!-- prettier-ignore-start -->
<!-- markdownlint-disable -->
<table>
  <tr>
    <td align="center"><a href="https://github.com/marcmarina"><img src="https://avatars3.githubusercontent.com/u/38327883?v=4" width="100px;" alt=""/><br /><sub><b>Marc Marina Miravitlles</b></sub></a><br /><a href="https://github.com/laxsmi/academico/commits?author=marcmarina" title="Code">💻</a></td>
  </tr>
</table>

<!-- markdownlint-enable -->
<!-- prettier-ignore-end -->
<!-- ALL-CONTRIBUTORS-LIST:END -->

This project follows the [all-contributors](https://github.com/all-contributors/all-contributors) specification. Contributions of any kind welcome!