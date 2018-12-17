# academico
[work-in-progress] Lavarel-based school management platform

Features

Course management

Student management

Teacher management

Rooms management

Reports


Installation

Requirements

php-bcmath
+ Laravel requirements


git clone

cd academico

cp .env.example .env

Edit the .env file with your DB info

php artisan key:generate

composer install

npm install

php artisan vendor:publish --provider="Backpack\Base\BaseServiceProvider"

npm run dev

php artisan migrate
