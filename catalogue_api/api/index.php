<?php

require_once "../src/vendor/autoload.php";


$errors = require 'conf/errors.php';
$configuration = new \Slim\Container(['settings' => ['displayErrorDetails' => true]]);
$app_config = array_merge($errors);
$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true,
        'debug' => true,
        'whoops.editor' => 'sublime',
    ]]);

$app->get('/docs[/]', function (Request $request, Response $response, $args) {
    return $response->write(file_get_contents('docs/index.html'));
});

$app->get('/', function (Request $request, Response $response, $args) {
    $scheme = $request->getUri()->getScheme();
    $host = $request->getUri()->getHost();
    $port = $request->getUri()->getPort();
    $path = $request->getUri()->getPath();
    $docURL = "$scheme://$host:$port$path" . "docs/";

    $response = $response->withHeader("Location", $docURL);
    return $response;
});

$app->get('/categories/{id}/{sandwichs}[/]', \lbs\command\control\CategoriesController::class . ':getCategorieSandwich');
$app->get('/categories/{id}[/]', \lbs\command\control\CategoriesController::class . ':getCategorie');
$app->get('/categories[/]', \lbs\command\control\CategoriesController::class . ':getCategories');
$app->get('/sandwichs[/]', \lbs\command\control\CategoriesController::class . ':getSandwichs');
$app->get('/sandwich/{uri}[/]', function ($rq, $rs, $args) {
    return (new \lbs\command\control\CategoriesController($this))->getNameSandwich($rq, $rs, $args);
});
$app->run();