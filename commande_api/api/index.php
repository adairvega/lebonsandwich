<?php
require '../src/vendor/autoload.php';
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use \lbs\common\bootstrap\Eloquent;
<<<<<<< HEAD

$config = parse_ini_file("conf/conf.ini");
var_dump($config);
=======
use GuzzleHttp\Client;

$config = parse_ini_file("../src/conf/conf.ini");
>>>>>>> c3c34b4d701bc0e258fdd584775a3af74150a69a
$db = new Illuminate\Database\Capsule\Manager();
$db->addConnection($config);
$db->setAsGlobal();
$db->bootEloquent();

$errors = require './conf/errors.php';

$configuration = new \Slim\Container(['settings' => ['displayErrorDetails' => true]]);

$app_config = array_merge($errors);
<<<<<<< HEAD

$app = new \Slim\App(new \Slim\Container($app_config,$configuration));
=======
$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true,
        'debug' => true,
        'whoops.editor' => 'sublime',
    ]]);

$container = $app->getContainer();
$container['guzzle'] = function ($container) {
    $myService = new \GuzzleHttp\Client();
    return $myService;
};

function checkToken(Request $rq, Response $rs, callable $next)
{
    $id = $rq->getAttribute('route')->getArgument('id');
    $token = $rq->getQueryParams('token', null);
    try {
        \lbs\command\model\Commande::where('id', '=', $id)
            ->where('token', '=', $token)
            ->firstOrFail();
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
        return $rs;
    }
    return $next($rq, $rs);
}
>>>>>>> c3c34b4d701bc0e258fdd584775a3af74150a69a

$app->get('/commandes[/]', function ($rq, $rs, $args) {
    return (new \lbs\command\control\CommandesController($this))->getCommands($rq, $rs, $args);
});
$app->get('/commandes/{id}[/]', function ($rq, $rs, $args) {
    return (new \lbs\command\control\CommandesController($this))->getCommand($rq, $rs, $args);
})->add("checkToken");
$app->post('/commandes/{nom}/{mail}', function ($rq, $rs, $args) {
    return (new \lbs\command\control\CommandesController($this))->insertCommand($rq, $rs, $args);
});
$app->put('/commandes/{id}/{data}/{value}', function ($rq, $rs, $args) {
    return (new \lbs\command\control\CommandesController($this))->updateCommand($rq, $rs, $args);
});
$app->run();
