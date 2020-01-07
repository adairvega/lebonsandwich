<?php
require '../src/vendor/autoload.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use \lbs\common\bootstrap\Eloquent;

$config = parse_ini_file("../src/conf/conf.ini");	/* une instance de connexion  */
$db = new Illuminate\Database\Capsule\Manager();
$db->addConnection($config); 	/* configuration avec nos paramètres */
$db->setAsGlobal();            	/* rendre la connexion visible dans tout le projet */
$db->bootEloquent();           	/* établir la connexion */

$configuration = new \Slim\Container(['settings' => ['displayErrorDetails' => true]]);
$app = new \Slim\App($configuration);

$container = $app->getContainer();
$container["errorHandler"] = function ($container) {
    return new CustomHandler;
};

$app->get('/commandes[/]', \lbs\command\control\CommandesController::class.':getCommands');

$app->get('/commandes/{id}[/]', \lbs\command\control\CommandesController::class.':getCommand')
		->setName('commande_api');

$app->run();
