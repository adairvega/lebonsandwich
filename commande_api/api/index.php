<?php
require '../src/vendor/autoload.php';
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use \lbs\common\bootstrap\Eloquent;
use GuzzleHttp\Client;

$config = parse_ini_file("conf/conf.ini");
$db = new Illuminate\Database\Capsule\Manager();
$db->addConnection($config);
$db->setAsGlobal();
$db->bootEloquent();

$errors = require './conf/errors.php';

$configuration = new \Slim\Container(['settings' => ['displayErrorDetails' => true]]);
$app_config = array_merge($errors);
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
