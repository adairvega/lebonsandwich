<?php

require_once __DIR__.'/../src/vendor/autoload.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use \lbs\common\bootstrap\Eloquent;

$connection = new MongoDB\Client("mongodb://dbcat:27017");
$db = $connection->catalogue;

$errors = require './conf/errors.php';
$configuration = new \Slim\Container(['settings' => ['displayErrorDetails' => true]]);
$app_config = array_merge($errors);
$app = new \Slim\App(new \Slim\Container($app_config));

$app->get('/categories[/]', \lbs\command\control\CategoriesController::class . ':getCategories');

$app->run();