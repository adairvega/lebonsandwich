<?php

require_once "../src/vendor/autoload.php";

$errors = require './conf/errors.php';
$configuration = new \Slim\Container(['settings' => ['displayErrorDetails' => true]]);
$app_config = array_merge($errors);
$app = new \Slim\App(new \Slim\Container($app_config));
$c = new \MongoDB\Client("mongodb://dbcat");

$app->get('/categories[/]', \lbs\command\control\CategoriesController::class . ':getCategorie');

$app->run();