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

$configuration = new \Slim\Container(['settings' => ['displayErrorDetails' => true]]);
$app = new \Slim\App($configuration);


unset($app->getContainer()['notFoundHandler']);
$app->getContainer()['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        $response = new \Slim\Http\Response(404);
        return $response->withHeader('Content-Type', 'application/json;charset=utf-8')
            ->write("Page not found");
    };
};


$c = $app->getContainer();
$c['notAllowedHandler'] = function ($c) {
    return function ($request, $response, $methods) use ($c) {
        return $response->withStatus(405)
            ->withHeader('Allow', implode(', ', $methods))
            ->withHeader('Content-type', 'application/json;charset=utf-8')
            ->write('Method must be one of: ' . implode(', ', $methods));
    };
};

$c = $app->getContainer();
$c['phpErrorHandler'] = function ($c) {
    return function ($request, $response, $error) use ($c) {
        return $response->withStatus(500)
            ->withHeader('Content-Type', 'application/json;charset=utf-8')
            ->write('Something went wrong! DD');
    };
};

$app->get('/commandes[/]', \lbs\command\control\CommandesController::class.':getCommands');
$app->get('/commandes/{id}[/]', \lbs\command\control\CommandesController::class.':getCommand')->setName('commande_api');
$app->post('/contacts/{nom}[/]',function(Request $req, Response $resp, array $args) {
    $commande_test = new commande();
    $commande_test->id = "2222";
    $commande_test->nom = $args['nom'];
    $commande_test->livraison = "2019-11-10 13:05:56";
    $commande_test->mail = "dede";
    $commande_test->save();
    $rs = $resp->withStatus(201)
        ->withHeader('Location','http://api.commande.local:19080/commandes/'.$commande_test->id)
        ->withHeader('Content-Type', 'application/json;charset=utf-8');
    $rs->getBody()->write($sushi = commande::find($commande_test->id));
    return $rs;
});
$app->run();
