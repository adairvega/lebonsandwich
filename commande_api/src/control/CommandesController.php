<?php

namespace lbs\command\control;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use MongoDB\Driver\WriteError;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use \lbs\command\model\Commande as commande;

class CommandesController
{
    protected $c;

    public function __construct(\Slim\Container $c = null)
    {
        $this->c = $c;
    }

    public function getCommands(Request $req, Response $resp, array $args)
    {

        try {

            $cde = \lbs\command\model\Commande::all();

            $rs = $resp->withStatus(200)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');

            $rs->getBody()->write(json_encode([
                "type" => "collection",
                "count" => $cde,
                "commandes" => $cde]));

            return $rs;
<<<<<<< HEAD
        }catch (\Exception $e){
=======
        } catch (\Exception $e) {
            echo "HOla";
>>>>>>> 92aa908e8c5e203035d0429823f34ac89022fd72
            return Writer::json_error($rs, 404, $e->getMessage());
        }

    }

    public function getCommand(Request $req, Response $resp, array $args)
    {
        try {
            $id = $args['id'];

            $cde = \lbs\command\model\Commande::findOrFail($i);

            $rs = $resp->withStatus(200)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');

            $rs->getBody()->write(json_encode([
                "type" => "collection",
                "commandes" => $cde]));

            return $rs;

        } catch (ModelNotFoundException $e) {

            $rs = $resp->withStatus(404)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
<<<<<<< HEAD
            $rs->getBody()
                ->write(json_encode(['Error_code'=>404, 'Error message'=>$e
                ->getMessage()]));

            return $rs;
        }
	}

    public function createCommand(Request $req, Response $resp, array $args){

        $cde_data = $req->getParsedBody();

        try{

            $new_cde = new \lbs\command\model\Commande;
            //construire un token d'authentification aléatoire
            $new_cde->id = Uuid::uuid4();
            //supprime les caractères spéciaux d'une chaîne de caractères
            $new_cde->nom = filter_var($cde_data['nom_client'], FILTER_SANITIZE_STRING);
            //supprime les caractères qui ne font pas partie d'une adresse email
            $new_cde->mail = filter_var($cde_data['mail_client'], FILTER_SANITIZE_EMAIL);
            $new_cde->livraison = DateTime::createFromFormat('d-m-Y H:i'.$cde_data['livraison']['date'].''.$cde_data['livraison']['heure']);

            $new_cde->status = \lbs\command\model\Commande::CREATED;

            $new_cde->save();

            return $resp->withStatus(201)
                        ->getBody()
                        ->write(json_encode(['commande' => $new_cde->toArray()]))
                        ->withHeader('Location',$this->new_cde['router']
                        ->pathFor('commande', ['id' => $c->id]));

        }catch(\Exception $e){

            return Writer::json_error($resp, 500, $e->getMessage());
=======
            $rs->getBody()->write(json_encode(['Error_code' => 404, 'Error message' => $e->getMessage()]));

            return $rs;
        }
    }

    public function insertCommand(Request $req, Response $resp, array $args)
    {
        try {
            if (filter_var($args['mail'], FILTER_VALIDATE_EMAIL) == !0) {
                $commande_test = new commande();
                $commande_test->id = uniqid();
                $commande_test->nom = (filter_var($args['nom'], FILTER_SANITIZE_STRING));
                $commande_test->livraison = date("Y-m-d h:i:s");
                $commande_test->mail = filter_var($args['mail'], FILTER_VALIDATE_EMAIL);
                $commande_test->save();
                $rs = $resp->withStatus(201)
                    ->withHeader('Location', 'http://api.commande.local:19080/commandes/' . $commande_test->id)
                    ->withHeader('Content-Type', 'application/json;charset=utf-8');
                $rs->getBody()->write(json_encode(commande::find($commande_test->id)));
                return $rs;
            } else {
                echo "please insert a valid email address";
            }
        } catch (ModelNotFoundException $e) {
            $rs = $resp->withStatus(404)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode(['Error_code' => 404, 'Error message' => $e->getMessage()]));
            return $rs;
        }
    }


    public function updateCommand(Request $req, Response $resp, array $args)
    {
        if ($commande_test = commande::find($args["id"])) {
            switch ($args["data"]) {
                case "mail":
                    if (filter_var($args['value'], FILTER_VALIDATE_EMAIL) == !0) {
                        $commande_test->mail = filter_var($args['value'], FILTER_VALIDATE_EMAIL);
                        $commande_test->save();
                    } else {
                        echo "please use a valid email format";
                    }
                    break;
                case "nom":
                    $commande_test->nom = filter_var($args['value'], FILTER_SANITIZE_STRING);
                    $commande_test->save();
                    break;
            }
            $rs = $resp->withStatus(200)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode($commande_test));
            return $rs;
        } else {
            $rs = $resp->withStatus(404)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode(['Error_code' => 404, 'please enter an existing id']));
            return $rs;
>>>>>>> 92aa908e8c5e203035d0429823f34ac89022fd72
        }
    }
}
