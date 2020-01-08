<?php

namespace lbs\command\control;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use MongoDB\Driver\WriteError;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CommandesController {
	protected $c;

	public function __construct( \Slim\Container $c = null){
		$this->c = $c;
	}

	public function getCommands(Request $req, Response $resp, array $args) {

        try {

	    $cde = \lbs\command\model\Commande::all();

		$rs = $resp->withStatus(200)
					->withHeader('Content-Type', 'application/json;charset=utf-8');

		$rs->getBody()->write(json_encode([
					"type" => "collection",
					"count" => $cde,
					"commandes" => $cde]));

            return $rs;
        }catch (\Exception $e){
            return Writer::json_error($rs, 404, $e->getMessage());
        }

	}

	public function getCommand(Request $req, Response $resp, array $args) {
        try {
            $id = $args['id'];

            $cde = \lbs\command\model\Commande::findOrFail($i);

            $rs = $resp->withStatus(200)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');

            $rs->getBody()->write(json_encode([
                "type" => "collection",
                "commandes" => $cde]));

            return $rs;

        }catch(ModelNotFoundException $e){

            $rs = $resp->withStatus(404)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()
                ->write(json_encode(['Error_code'=>404, 'Error message'=>$e
                ->getMessage()]));

            return $rs;
        }
	}
}
