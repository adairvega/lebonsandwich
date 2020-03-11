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
});

$app->get('/commandes/{id}[/]', function ($rq, $rs, $args) {
    return (new lbs\command\control\CommandesController($this))->getCommand($rq, $rs, $args);
})->add(lbs\command\control\Middleware::class . ':getToken')->add(\lbs\command\control\Middleware::class . ':checkToken');

$app->get('/commandes/{id}/items', function ($rq, $rs, $args) {
    return (new lbs\command\control\CommandesController($this))->getCommandItems($rq, $rs, $args);
})->add(lbs\command\control\Middleware::class . ':getToken')->add(\lbs\command\control\Middleware::class . ':checkToken');

//todo do we need to get the user info from the uri or from the request's body?
$app->post('/commandes', function ($rq, $rs, $args) {
    return (new lbs\command\control\CommandesController($this))->insertCommand($rq, $rs, $args);
})->add(new Validation(lbs\command\api\middlewares\CommandValidator::class . ':validators'))->add(lbs\command\control\Middleware::class . ':decodeJWT')->add(lbs\command\control\Middleware::class . ':checkJWT')->add(lbs\command\control\Middleware::class . ':checkEmail');

$app->put('/commandes/{id}/{data}/{value}', function ($rq, $rs, $args) {
    return (new lbs\command\control\CommandesController($this))->updateCommand($rq, $rs, $args);
});
$app->post('/clients/{user_id}/auth', function ($rq, $rs, $args) {
    return (new lbs\command\control\UserController($this))->userAuthentication($rq, $rs, $args);
})->add(lbs\command\control\Middleware::class . ':decodeAuthorization')->add(lbs\command\control\Middleware::class . ':checkAuthorization');

$app->get('/clients/{user_id}', function ($rq, $rs, $args) {
    return (new lbs\command\control\UserController($this))->userProfile($rq, $rs, $args);
})->add(lbs\command\control\Middleware::class . ':decodeJWT')->add(lbs\command\control\Middleware::class . ':checkJWT');

$app->run();
