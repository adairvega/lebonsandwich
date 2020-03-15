<?php
require '../src/vendor/autoload.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use \lbs\common\bootstrap\Eloquent;
use \DavidePastore\Slim\Validation\Validation as Validation;

$config = parse_ini_file("../src/conf/conf.ini");
$db = new Illuminate\Database\Capsule\Manager();
$db->addConnection($config);
$db->setAsGlobal();
$db->bootEloquent();

$errors = require 'conf/errors.php';
$configuration = new \Slim\Container(['settings' => ['displayErrorDetails' => true]]);
$app_config = array_merge($errors);
$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true,
        'debug' => true,
        'whoops.editor' => 'sublime',
    ]]);

$app->get('/commandes[/]', function ($rq, $rs, $args) {
    return (new lbs\command\control\CommandesController($this))->getCommands($rq, $rs, $args);
})->add(lbs\command\control\Middleware::class . ':headersCORS')->add(lbs\command\control\Middleware::class . ':checkHeaderOrigin');

$app->get('/commandes/{id}[/]', function ($rq, $rs, $args) {
    return (new lbs\command\control\CommandesController($this))->getCommand($rq, $rs, $args);
})->add(lbs\command\control\Middleware::class . ':headersCORS')->add(lbs\command\control\Middleware::class . ':checkHeaderOrigin')->add(lbs\command\control\Middleware::class . ':getToken')->add(\lbs\command\control\Middleware::class . ':checkToken');

$app->get('/commandes/{id}/items', function ($rq, $rs, $args) {
    return (new lbs\command\control\CommandesController($this))->getCommandItems($rq, $rs, $args);
})->add(lbs\command\control\Middleware::class . ':headersCORS')->add(lbs\command\control\Middleware::class . ':checkHeaderOrigin')->add(lbs\command\control\Middleware::class . ':getToken')->add(\lbs\command\control\Middleware::class . ':checkToken');

$app->post('/commandes', function ($rq, $rs, $args) {
    return (new lbs\command\control\CommandesController($this))->insertCommand($rq, $rs, $args);
})->add(lbs\command\control\Middleware::class . ':headersCORS')->add(lbs\command\control\Middleware::class . ':checkHeaderOrigin')->add(new Validation(lbs\command\control\CommandValidator::validators()))->add(lbs\command\control\Middleware::class . ':decodeJWT')->add(lbs\command\control\Middleware::class . ':checkJWT');

$app->put('/commandes/{id}/{data}/{value}', function ($rq, $rs, $args) {
    return (new lbs\command\control\CommandesController($this))->updateCommand($rq, $rs, $args);
})->add(lbs\command\control\Middleware::class . ':headersCORS')->add(lbs\command\control\Middleware::class . ':checkHeaderOrigin');

$app->post('/clients/{user_id}/auth', function ($rq, $rs, $args) {
    return (new lbs\command\control\UserController($this))->userAuthentication($rq, $rs, $args);
})->add(lbs\command\control\Middleware::class . ':headersCORS')->add(lbs\command\control\Middleware::class . ':checkHeaderOrigin')->add(lbs\command\control\Middleware::class . ':decodeAuthorization')->add(lbs\command\control\Middleware::class . ':checkAuthorization');

$app->get('/clients/{user_id}', function ($rq, $rs, $args) {
    return (new lbs\command\control\UserController($this))->userProfile($rq, $rs, $args);
})->add(lbs\command\control\Middleware::class . ':headersCORS')->add(lbs\command\control\Middleware::class . ':checkHeaderOrigin')->add(lbs\command\control\Middleware::class . ':decodeJWT')->add(lbs\command\control\Middleware::class . ':checkJWT');

$app->run();
