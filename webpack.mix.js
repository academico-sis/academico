    const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
    .js('resources/js/register-bundle/register.js', 'public/js')
    .vue()
    .extract()
    .version();
