<?php

require '../src/vendor/autoload.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

<<<<<<< HEAD
$config = parse_ini_file("conf/conf.ini");
/* une instance de connexion  */
=======

$config = parse_ini_file("../src/conf/conf.ini");
>>>>>>> 92aa908e8c5e203035d0429823f34ac89022fd72
$db = new Illuminate\Database\Capsule\Manager();
$db->addConnection($config);
$db->setAsGlobal();
$db->bootEloquent();

$errors = require 'conf/errors.php';
$configuration = new \Slim\Container(['settings' => ['displayErrorDetails' => true]]);
<<<<<<< HEAD

$app = new \Slim\App($configuration);

unset($app->getContainer()['notFoundHandler']);

$app->getContainer()['notFoundHandler'] = function ($configuration){
		return function (Request $req, Response $resp) use ($configuration){
			$resp = new \Slim\Http\Response(404);
        		return $resp->withHeader('Content-Type', 'application/json;charset=utf-8')
            ->write("Page not found");
    };
};

$app->getContainer()['notFoundHandler'] = function ($configuration){
		return function (Request $req, Response $resp) use ($configuration){
			$resp = new \Slim\Http\Response(400);
				return $resp->withHeader('Content-Type', 'application/json;charset=utf-8')
							->write('Ressource non disponible');
		};
	};

$app->getContainer()['notAllowedHandler'] = function ($configuration){
	return function (Request $req, Response $resp, $methods) use ($configuration){
			$resp = new \Slim\Http\Response(405);
				return $resp->withHeader('Allow', implode(',', $methods))
							->write('Méthode permises : ' . implode(',', $methods));
		};
};

$app->getContainer()['phpErrorHandler'] = function ($configuration){
	return function (Request $req, Response $resp, $e) use ($configuration){
			$resp = new \Slim\Http\Response(500);
				$resp->getBody()
					->write('error :' . $e->getMessage())
					->write('file :' . $e->getFile())
					->write('line :' . $e->getLine());

		};
};

$app->get('/commandes[/]', \lbs\command\control\CommandesController::class.':getCommands');

$app->get('/commandes/{id}[/]', \lbs\command\control\CommandesController::class.':getCommand')
		->setName('commande_api');

$app->post('/commandes', \lbs\command\control\CommandesController::class.':createCommand');

$app->run();
=======
$app_config = array_merge($errors);
$app = new \Slim\App(new \Slim\Container($app_config,$configuration));

$app->get('/commandes[/]', \lbs\command\control\CommandesController::class . ':getCommands');
$app->get('/commandes/{id}[/]', \lbs\command\control\CommandesController::class . ':getCommand')->setName('commande_api');
$app->post('/commandes/{nom}/{mail}', \lbs\command\control\CommandesController::class . ':insertCommand');
$app->put('/commandes/{id}/{data}/{value}', \lbs\command\control\CommandesController::class . ':updateCommand');
$app->run();
>>>>>>> 92aa908e8c5e203035d0429823f34ac89022fd72
