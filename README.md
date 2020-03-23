# About this project
Academico is an open-source, Lavarel-based school management platform. Its main features include course management, enrolments management, resources scheduling, reports and stats. It is primarily targeted at small and medium-sized institutions who need a simple and affordable solution to manage their school and courses.

See overview and features at https://academico.site

# License
This software is is based on Backpack for Laravel, which means you can test if for free, but you will need to [acquire a Backpack license](https://backpackforlaravel.com/pricing) if you decide to use it for _commercial_ use.


# Installation


## Requirements
See https://laravel.com/docs/installation#server-requirements

The following instructions assume that you are somehow familiar with Laravel.

## Download and install the code
* `git clone`
* Copy `.env.example` to `.env` and  edit the file with your database settings
* `php artisan key:generate`
* `composer install`
* `php artisan migrate`
* `php artisan db:seed`

## Run the application
* `php artisan serve`

If all went well, you should be able to open the application in your browser at `http://127.0.0.1:8000`

Login using username `admin@academico.site` and password `secret`

## Set up in production

You need to install a webserver according to the Laravel documentation when you're ready to move the application to production

Some features will also require a queue worker.

# Documentation
* Technical documentation -> https://github.com/laxsmi/academico/wiki
* User documentation -> https://laxsmi.gitbook.io/academico-docs/
