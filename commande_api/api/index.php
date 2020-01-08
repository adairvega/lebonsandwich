<?php
require '../src/vendor/autoload.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$config = parse_ini_file("conf/conf.ini");
/* une instance de connexion  */
$db = new Illuminate\Database\Capsule\Manager();
$db->addConnection($config); 	/* configuration avec nos paramètres */
$db->setAsGlobal();            	/* rendre la connexion visible dans tout le projet */
$db->bootEloquent();           	/* établir la connexion */

$configuration = new \Slim\Container(['settings' => ['displayErrorDetails' => true]]);

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

$app->run();
