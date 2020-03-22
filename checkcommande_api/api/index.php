<?php

require '../src/vendor/autoload.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use \lbs\common\bootstrap\Eloquent;

$config = parse_ini_file("../src/conf/conf.ini");
$db = new Illuminate\Database\Capsule\Manager();
$db->addConnection($config);
$db->setAsGlobal();
$db->bootEloquent();

$errors = require './conf/errors.php';
$configuration = new \Slim\Container(['settings' => ['displayErrorDetails' => true]]);
$app_config = array_merge($errors);
$app = new \Slim\App(new \Slim\Container($app_config));

$app->get('/commandes[/]', \lbs\command\control\PointVenteController::class . ':getCommands');
$app->get('/commandes/{id}[/]', \lbs\command\control\PointVenteController::class . ':getCommand')->setName('commande_api');
$app->get('/commandes/{id}/items[/]', \lbs\command\control\PointVenteController::class . ':getItems')->setName('commande_api');
$app->put('/commandes/{id}', function ($rq, $rs, $args) {
    return (new lbs\command\control\PointVenteController($this))->updateCommand($rq, $rs, $args);
});
$app->run();
