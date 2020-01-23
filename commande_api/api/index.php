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

$errors = require 'conf/errors.php';

$configuration = new \Slim\Container(['settings' => ['displayErrorDetails' => true]]);

$app_config = array_merge($errors);

$app = new \Slim\App(new \Slim\Container($app_config,$configuration));

$app->get('/commandes[/]', \lbs\command\control\CommandesController::class . ':getCommands');

$app->get('/commandes/{id}[/]', \lbs\command\control\CommandesController::class . ':getCommand')->setName('commande_api');

$app->post('/commandes/{nom}/{mail}', \lbs\command\control\CommandesController::class . ':insertCommand');

$app->put('/commandes/{id}/{data}/{value}', \lbs\command\control\CommandesController::class . ':updateCommand');

$app->run();