<?php
require '../src/vendor/autoload.php';
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use \lbs\common\bootstrap\Eloquent;
use \lbs\command\model\Commande as commande;

$config = parse_ini_file("../src/conf/conf.ini");
$db = new Illuminate\Database\Capsule\Manager();
$db->addConnection($config);
$db->setAsGlobal();
$db->bootEloquent();

$errors = require 'errors.php';
$configuration = new \Slim\Container(['settings' => ['displayErrorDetails' => true]]);
$app_config = array_merge($errors);
$app = new \Slim\App(new \Slim\Container($app_config));

$app->get('/commandes[/]', \lbs\command\control\CommandesController::class.':getCommands');
$app->get('/commandes/{id}[/]', \lbs\command\control\CommandesController::class.':getCommand')->setName('commande_api');
$app->post('/contacts/{nom}[/]',function(Request $req, Response $resp, array $args) {
    $commande_test = new commande();
    $commande_test->id = "1111111111111";
    $commande_test->nom = $args['nom'];
    $commande_test->livraison = date("Y-m-d h:i:s");
    $commande_test->mail = "dede";
    $commande_test->save();
    $rs = $resp->withStatus(201)
        ->withHeader('Location','http://api.commande.local:19080/commandes/'.$commande_test->id)
        ->withHeader('Content-Type', 'application/json;charset=utf-8');
    $rs->getBody()->write($sushi = commande::find($commande_test->id));
    return $rs;
});
$app->run();
