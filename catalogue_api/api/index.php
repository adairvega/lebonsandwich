<?php

require_once "../src/vendor/autoload.php";


$errors = require 'conf/errors.php';
$configuration = new \Slim\Container(['settings' => ['displayErrorDetails' => true]]);
$app_config = array_merge($errors);
$app = new \Slim\App();

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

$app->get('/categories/{id}/sandwichs[/]', function ($rq, $rs, $args) {
    return (new \lbs\command\control\CategoriesController($this))->getCategorieSandwich($rq, $rs, $args);
});
$app->get('/categories/{id}[/]', function ($rq, $rs, $args) {
    return (new \lbs\command\control\CategoriesController($this))->getCategorie($rq, $rs, $args);
});
$app->get('/categories[/]', function ($rq, $rs, $args) {
    return (new \lbs\command\control\CategoriesController($this))->getCategories($rq, $rs, $args);
});
$app->get('/sandwichs[/]', function ($rq, $rs, $args) {
    return (new \lbs\command\control\CategoriesController($this))->getSandwichs($rq, $rs, $args);
});
$app->get('/sandwichs/{uri}[/]', function ($rq, $rs, $args) {
    return (new \lbs\command\control\CategoriesController($this))->getNameSandwich($rq, $rs, $args);
});
$app->run();