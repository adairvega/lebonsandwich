<?php

namespace lbs\command\control;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use MongoDB\Driver\WriteError;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use \lbs\command\model\Commande as commande;

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
            echo "HOla";
            return Writer::json_error($rs, 404, $e->getMessage());
        }

	}

	public function getCommand(Request $req, Response $resp, array $args) {
        try {
            $id = $args['id'];

            $cde = \lbs\command\model\Commande::findOrFail($id);

            $rs = $resp->withStatus(200)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');

            $rs->getBody()->write(json_encode([
                "type" => "collection",
                "commandes" => $cde]));

            return $rs;

        }catch(ModelNotFoundException $e){

            $rs = $resp->withStatus(404)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode(['Error_code'=>404, 'Error message'=>$e->getMessage()]));

            return $rs;
        }
	}

    public function insertCommand(Request $req, Response $resp, array $args) {
        try {
            if (filter_var($args['mail'], FILTER_VALIDATE_EMAIL) ==! 0){
                $commande_test = new commande();
                $commande_test->id = uniqid();
                $commande_test->nom = (filter_var($args['nom'],FILTER_SANITIZE_STRING));
                $commande_test->livraison = date("Y-m-d h:i:s");
                $commande_test->mail = filter_var($args['mail'], FILTER_VALIDATE_EMAIL);
                $commande_test->save();
                $rs = $resp->withStatus(201)
                    ->withHeader('Location','http://api.commande.local:19080/commandes/'.$commande_test->id)
                    ->withHeader('Content-Type', 'application/json;charset=utf-8');
                $rs->getBody()->write(json_encode(commande::find($commande_test->id)));
                return $rs;
            }else{
                echo "please insert a valid email address";
            }
        }catch(ModelNotFoundException $e){
            $rs = $resp->withStatus(404)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode(['Error_code'=>404, 'Error message'=>$e->getMessage()]));
            return $rs;
        }
    }
}
