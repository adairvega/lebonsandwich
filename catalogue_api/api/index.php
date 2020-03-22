<?php

require_once "../src/vendor/autoload.php";

$errors = require './conf/errors.php';
$configuration = new \Slim\Container(['settings' => ['displayErrorDetails' => true]]);
$app_config = array_merge($errors);
$app = new \Slim\App(new \Slim\Container($app_config));

$app->get('/categories/{id}/{sandwichs}[/]', \lbs\command\control\CategoriesController::class . ':getCategorieSandwich');
$app->get('/categories/{id}[/]', \lbs\command\control\CategoriesController::class . ':getCategorie');
$app->get('/categories[/]', \lbs\command\control\CategoriesController::class . ':getCategories');
$app->get('/sandwichs[/]', \lbs\command\control\CategoriesController::class . ':getSandwichs');
$app->get('/sandwich/{uri}[/]', function ($rq, $rs, $args) {
    return (new \lbs\command\control\CategoriesController($this))->getNameSandwich($rq, $rs, $args);
});
$app->run();