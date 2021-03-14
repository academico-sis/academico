<?php
namespace Deployer;

require 'recipe/laravel.php';

// Project name
set('application', 'academico');

// Project repository
set('repository', 'git@github.com:academico-sis/afcuenca.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

// Shared files/dirs between deploys
add('shared_files', []);
add('shared_dirs', []);

// Writable dirs by web server
add('writable_dirs', []);


// Hosts

host('afc2.thomasdebay.com')
    ->user('afcuenca')
    ->set('branch', 'master')
    ->set('deploy_path', '/var/www/academico.afcuenca.org.ec');

task('build', function () {
    run('cd {{release_path}} && composer install --optimize-autoloader --no-dev');
});

task('cache', function () {
    run('cd {{release_path}} && php artisan cache:clear && php artisan config:cache && php artisan route:cache && php artisan route:cache && php artisan view:cache');
});

task('enlightn', function () {
    run('cd {{release_path}} && php artisan enlightn');
});

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.

before('deploy:symlink', 'artisan:migrate');
