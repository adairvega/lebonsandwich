<?php

namespace lbs\command\control;

use Doctrine\Instantiator\Exception\ExceptionInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use lbs\command\model\Item;
use MongoDB\Driver\WriteError;
use phpDocumentor\Reflection\Types\Integer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use \lbs\command\model\Commande as commande;

class PointVenteController
{
    protected $c;

    public function __construct(\Slim\Container $c = null)
    {
        $this->c = $c;
    }

    public function getCommand(Request $req, Response $resp, array $args)
    {
        try {
            $id = $args['id'];
            $cde = commande::findOrFail($id);
            $items = $cde->commandeItems()->select("uri", "libelle", "tarif", "quantite")->get();
            $order = array();
            $order["id"] = $cde->id;
            $order["created_at"] = $cde->created_at;
            $order["livraison"] = $cde->livraison;
            $order["nom"] = $cde->nom;
            $order["mail"] = $cde->mail;
            $order["montant"] = $cde->montant;
            $order["items"] = $items;
            $links = array(
                "self" => "http://api.checkcommande.local:19280/commandes/" . $id,
                "items" => "http://api.checkcommande.local:19280/commands/" . $id . "/items"
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


    public function updateCommand(Request $req, Response $resp, array $args)
    {
        if ($commande = commande::find($args["id"])) {
            $body = $req->getParsedBody();
            if (!empty($body["status"])) {
                if ($body["status"] > 0 and $body["status"] <= 4) {
                    $commande->status = $body["status"];
                    $commande->save();
                    $rs = $resp->withStatus(200)
                        ->withHeader('Content-Type', 'application/json;charset=utf-8');
                    $rs->getBody()->write(json_encode($commande));
                    return $rs;
                } else {
                    $res = $resp->withStatus(404)
                        ->withHeader('Content-Type', 'application/json;charset=utf-8');
                    $res->getBody()->write(json_encode(['Error_code' => 404, 'status value expected to be between 1 and 4']));
                    return $res;
                }
            } else {
                $rs = $resp->withStatus(404)
                    ->withHeader('Content-Type', 'application/json;charset=utf-8');
                $rs->getBody()->write(json_encode(['Error_code' => 404, 'please send a status']));
                return $rs;
            }
        } else {
            $rs = $resp->withStatus(404)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode(['Error_code' => 404, 'no command existing with this id']));
            return $rs;
        }
    }


    public function getItems(Request $req, Response $resp, array $args)
    {
        try {
            $id = $args['id'];
            $cde = Commande::findOrFail($id);
            $items = $cde->commandeItems()->get();

            $rs = $resp->withStatus(200)->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode([
                "type" => "item",
                "id" => $id,
                "items" => $items
            ]));
            return $rs;
        } catch (ModelNotFoundException $e) {
            $rs = $resp->withStatus(404)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode(['Error_code' => 404, 'Error message' => $e->getMessage()]));
            return $rs;
        }
    }
}
