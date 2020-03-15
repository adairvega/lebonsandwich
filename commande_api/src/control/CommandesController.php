<?php

namespace lbs\command\control;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use lbs\command\model\Item as item;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use lbs\command\model\Commande as commande;
use Ramsey\Uuid\Uuid;
use GuzzleHttp\Client;

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
        } catch (\Exception $e) {
            echo "HOla";
            return Writer::json_error($rs, 404, $e->getMessage());
        }
    }

    public function getCommand(Request $req, Response $resp, array $args)
    {
        $id = $args['id'];
        $token = $req->getAttribute("token");
        $cdes = Commande::where('id', '=', $id)->where('token', '=', $token)->get();
        if (!$cdes->isEmpty()) {
            foreach ($cdes as $cde) {
                $items_commande = $cde->getItems()->get();
            }
            $links = array(
                "self" => "http://api.commande.local:19080/commandes/" . $id,

                "items" => "http://api.commande.local:19080/commandes/" . $id . "/items"
            );
            $order = array();
            $order["id"] = $cde->id;
            $order["livraison"] = $cde->livraison;
            $order["nom"] = $cde->nom;
            $order["mail"] = $cde->mail;
            $order["status"] = $cde->status;
            $order["montant"] = $cde->montant;
            foreach ($items_commande as $item) {
                $items = array();
                $items["uri"] = $item->uri;
                $items["libelle"] = $item->libelle;
                $items["tarif"] = $item->tarif;
                $items["quantite"] = $item->quantite;
                $order["items"][] = $items;
            }
            $rs = $resp->withStatus(200)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode([
                "type" => "resource",
                "links" => $links,
                "commande" => $order]));
            return $rs;
        } else {
            $rs = $resp->withStatus(404)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode(['Error_code' => 404, 'Error message' => "token no corresponding"]));
            return $rs;
        }
    }


    public function getCommandItems(Request $req, Response $resp, array $args)
    {
        $id = $args['id'];
        $token = $req->getAttribute("token");
        $cdes = Commande::where('id', '=', $id)->where('token', '=', $token)->get();
        if (!$cdes->isEmpty()) {
            foreach ($cdes as $cde) {
                $items_commande = $cde->getItems()->get();
            }
            foreach ($items_commande as $item) {
                $items = array();
                $items["uri"] = $item->uri;
                $items["libelle"] = $item->libelle;
                $items["tarif"] = $item->tarif;
                $items["quantite"] = $item->quantite;
                $order["items"][] = $items;
            }
            $rs = $resp->withStatus(200)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode([
                "type" => "resource",
                "items" => $order["items"]]));
            return $rs;
        } else {
            $rs = $resp->withStatus(404)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode(['Error_code' => 404, 'Error message' => "token no corresponding"]));
            return $rs;
        }
    }

    public function insertCommand(Request $req, Response $resp, array $args)
    {
        if ($req->getAttribute('has_errors')) {
            $errors = $req->getAttribute('errors');
            var_dump($errors);
        } else {
            $body = $req->getParsedBody();
            $client_id = $body["client_id"];
            $client_mail = $body["mail"];
            $client_nom = $body["nom"];
            $token = $req->getAttribute("token");
            $token_uid = $token->uid;
            if ($client_id == $token_uid) {
                $client = new Client(["base_uri" => "http://api.catalogue.local"]);
                $prix_commande = 0;
                $getBody = json_decode($req->getBody());
                foreach ($getBody->items as $item) {
                    $response = $client->get($item->uri);
                    $sandwichs = json_decode($response->getBody());
                    foreach ($sandwichs as $sandwich) {
                        $order = array();
                        $order["commande"]["uri"] = $sandwich->ref;
                        $order["commande"]["libelle"] = $sandwich->nom;
                        $order["commande"]["tarif"] = $sandwich->prix;
                        $order["commande"]["quantite"] = $item->q;
                        $orders["commandes"][] = $order;
                    }
                }
                $commande_test = new commande();
                $client = new \lbs\command\model\Client();
                $client = \lbs\command\model\Client::find($client_id);
                $commande_test->id = Uuid::uuid4();
                $token = random_bytes(32);
                $token = bin2hex($token);
                $commande_test->nom = (filter_var($client_nom, FILTER_SANITIZE_STRING));
                $commande_test->livraison = date("Y-m-d h:i:s");
                $commande_test->mail = (filter_var($client_mail, FILTER_SANITIZE_EMAIL));
                $commande_test->token = $token;
                foreach ($orders["commandes"] as $commande) {
                    $item = new item();
                    $item->uri = $commande["commande"]["uri"];
                    $item->libelle = $commande["commande"]["libelle"];
                    $item->tarif = $commande["commande"]["tarif"];
                    $item->quantite = $commande["commande"]["quantite"];
                    $item->command_id = $commande_test->id;
                    $item->save();
                    $prix_commande += $commande["commande"]["tarif"] * $commande["commande"]["quantite"];
                }
                $commande_test->montant = $prix_commande;
                $client->cumul_achats += $prix_commande;
                $commande_test->client_id = $client_id;
                $commande_test->save();
                $client->save();
                $rs = $resp->withStatus(201)
                    ->withHeader('Location', 'http://api.commande.local:19080/commandes/' . $commande_test->id)
                    ->withHeader('Content-Type', 'application/json;charset=utf-8');
                $rs->getBody()->write(json_encode([
                    "commande" => commande::select("nom", "mail", "livraison")->find($commande_test->id),
                    "id" => $commande_test->id,
                    "token" => $token,
                    "montant" => $prix_commande,
                    "items" => $getBody->items
                ]));
                return $rs;
            } else {
                $rs = $resp->withStatus(404)
                    ->withHeader('Content-Type', 'application/json;charset=utf-8');
                $rs->getBody()->write(json_encode(['Error_code' => 404, 'Error message' => "token and user id given not corresponding"]));
                return $rs;
            }
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
        }
    }

}
