<?php

namespace lbs\command\control;

use Doctrine\Instantiator\Exception\ExceptionInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use lbs\command\model\Item;
use MongoDB\Driver\WriteError;
use phpDocumentor\Reflection\Types\Integer;
use Ramsey\Uuid\Uuid;
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

    public function getCommand(Request $req, Response $resp, array $args)
    {
        try {
            $url = $_SERVER['REQUEST_URI'];
            $parts = parse_url($url);
            $id = $args['id'];
            $cde = commande::findOrFail($id);
            $items = $cde->commandeItems()->select("uri", "libelle", "tarif", "quantite")->get();
            $order = array();
            $order["id"] = $cde->id;
            $order["token"] = $cde->token;
            $order["created_at"] = $cde->created_at;
            $order["livraison"] = $cde->livraison;
            $order["nom"] = $cde->nom;
            $order["mail"] = $cde->mail;
            $order["montant"] = $cde->montant;
            $order["items"] = $items;
            $links = array(
                "self" => "http://api.checkcommande.local:19280/commandes/" . $id,
                "items" => "http://api.pointvente.local:19380/commands/" . $id . "/"
            );
            $rs = $resp->withStatus(200)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode([
                "type" => "resource",
                "links" => $links,
                "command" => $order]));
            return $rs;
        } catch (ModelNotFoundException $e) {
            $rs = $resp->withStatus(404)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
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
                $commande_test->token = Uuid::uuid4();
                $commande_test->nom = (filter_var($args['nom'], FILTER_SANITIZE_STRING));
                $commande_test->livraison = \DateTime::createFromFormat("Y-m-d h:i:s", $commande_test['livraison']['date']. '' .$commande_test['livraison']['heure']);
                $commande_test->mail = filter_var($args['mail'], FILTER_VALIDATE_EMAIL);
                $commande_test->montant = 0;
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
            $rs = $resp->withStatus(500)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode(['Error_code' => 500, 'Error message' => $e->getMessage()]));
            return $rs;
        }
    }
}
