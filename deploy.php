<?php
namespace Deployer;

require 'recipe/laravel.php';

// Project name
set('application', 'academico');

// Project repository
set('repository', 'git@github.com:laxsmi/academico.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true); 

// Shared files/dirs between deploys 
add('shared_files', []);
add('shared_dirs', []);

// Writable dirs by web server 
add('writable_dirs', []);


// Hosts

host('beta01.debay.org')
    ->user('deployer')    
    ->set('deploy_path', '/var/www/html/academico');    
    
// Tasks

task('build', function () {
    run('cd {{release_path}} && composer install');
});

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.

before('deploy:symlink', 'artisan:migrate');