<?php

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Response;
use Silex\Provider\DoctrineServiceProvider;
use App\Controller\StatusController;

$app = new Silex\Application();
$app->register(new DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver' => 'pdo_mysql',
        'host' => getenv('MYSQL_HOST') ?: 'mysql',
        'port' => intval(getenv('DATABASE_PORT') ?: 3306),
        'dbname' => getenv('DATABASE_NAME') ?: 'mysql',
        'user' => getenv('DATABASE_USER') ?: 'root',
        'password' => getenv('DATABASE_PASSWORD') ?: 'root',
        'charset' => 'utf8',
    ),
));

$app->get('/', function () {
    return new Response('<p>My PHP application running!</p><p><a href="/index.php/status">Go to status page to check the database.</a></p>');
});

$app->mount('/status', new StatusController());
$app->run();
