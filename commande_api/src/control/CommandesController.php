<?php

namespace lbs\command\control;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use lbs\command\model\Client as user;
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

    /**
     * @api {get} http://api.commande.local:19080/commandes/{id} Description d'une commande
     * @apiName getCommand
     * @apiGroup Commandes
     * @apiExample {curl} Example usage:
     *     curl http://api.commande.local:19080/commandes/cdf6302b-940b-4348-b913-3cb2052bf042?token=543fc479e422715feb9562809cdd9ca54528426fae2ec0ff2382a32b937555c3
     * @apiParam {Number} id Commande unique ID.
     * @apiParam {String} token token de la commande.
     * @apiSuccess {Array} links  Liens vers la commande ou les items de la commande.
     * @apiSuccess {Array} commande Description de la commande.
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *     "type": "collection",
     *     "count": [
     *              {
     *                  "id": "cdf6302b-940b-4348-b913-3cb2052bf042",
     *                  "created_at": "2019-11-08 13:45:55",
     *                  "updated_at": "2019-11-08 13:45:55",
     *                  "livraison": "2019-11-08 16:56:15",
     *                  "nom": "Dubois",
     *                  "mail": "Dubois@free.fr",
     *                  "montant": "40.25",
     *                  "remise": null,
     *                  "token": "543fc479e422715feb9562809cdd9ca54528426fae2ec0ff2382a32b937555c3",
     *                  "client_id": null,
     *                  "ref_paiement": null,
     *                  "date_paiement": null,
     *                  "mode_paiement": null,
     *                  "status": 1
     *               }
     *          ]
     *      }
     *   }
     */
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
            $rs = $resp->withStatus(400)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode(['Error_code' => 400, 'Error message' => "token no corresponding"]));
            return $rs;
        }
    }

    /**
     * @api {get} http://api.commande.local:19080/commandes/{id}/{items} Liste des items d'une commande
     * @apiName getCommandItems
     * @apiGroup Commandes
     *
     * @apiParam {Number} id Commande unique ID.
     *
     * @apiSuccess {Array} items  Liste des items d'une commande.
     */
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
            $rs = $resp->withStatus(400)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode(['Error_code' => 400, 'Error message' => "token no corresponding"]));
            return $rs;
        }
    }


    /**
     * @api {post} http://api.commande.local:19080/commandes/auth Créer une commande (authentifié).
     * @apiName insertCommandAuth
     * @apiGroup Commandes
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpc3MiOiJodHRwOlwvXC9hcGkuYmFja29mZmljZS5sb2NhbCIsImF1ZCI6Imh0dHA6XC9cL2FwaS5iYWNrb2ZmaWNlLmxvY2FsIiwiaWF0IjoxNTg0OTY5NzE3LCJleHAiOjE1ODQ5NzMzMTcsInVpZCI6IjA0MDg5NmVlLTg4M2MtNDBlMi1iNDA1LWVkMGU3NzIyOTlhNCIsImx2bCI6MX0.nhmmDPn-iHWCDVQTNOd1vQXHUG2V9Jw6Uk5Ml3oxooUaRId2wd1Bru1O3WFoUDA9K6MEO_Xp3CGqO3COvGAujw"
     *     }
     * @apiExample {curl} Example usage:
     *     curl -X POST http://api.commande.local:19080/commandes/auth
     * @apiParam {String} nom le nom du client.
     * @apiHeader {Bearer_Token}  Authorization JWT du client connecte.
     * @apiParam {String} mail l'adresse mail du client.
     * @apiParam {Number} client_id numero de l'id du client connecte.
     * @apiParam {Array} livraison Date et heure de la livraison.
     * @apiParam {Array} items uri et quantite des items souhaite.
     * @apiSuccess {ressource} commande  retourne les informations de la commande , le montant total et un token pour y acceder.
     * @apiParamExample {json} Request-Example:
     *  {
     *      "nom" : "test",
     *      "mail": "test@gmail.fr",
     *      "client_id" : 105,
     *      "livraison" : {
     *          "date": "7-12-2020",
     *          "heure": "12:30"
     *      },
     *       "items" : [
     *          { "uri": "/sandwichs/s19002", "q": 2}
     *          ]
     *   }
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *          "commande": {
     *              "nom": "test",
     *              "mail": "test@gmail.fr",
     *              "livraison": "2020-03-24 12:07:08"
     *      },
     *      "id": "be709896-f625-45b4-9157-ad301b672cea",
     *      "token": "5f21e110897750f6106a937ea29384ae4955dd4e96d5da7f6fe0ed43b0398266",
     *      "montant": 15.75,
     *      "items": [
     *          {
     *              "uri": "/sandwichs/s19005",
     *              "q": 3
     *          }
     *      ]
     * }
     */
    public function insertCommandAuth(Request $req, Response $resp, array $args)
    {
        if (!$req->getAttribute('has_errors')) {
            $getBody = $req->getBody();
            $json = json_decode($getBody, true);
            $client_id = $json["client_id"];
            $client_mail = $json["mail"];
            $client_nom = $json["nom"];
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
                $rs = $resp->withStatus(400)
                    ->withHeader('Content-Type', 'application/json;charset=utf-8');
                $rs->getBody()->write(json_encode(['Error_code' => 400, 'Error message' => "token and user id given not corresponding"]));
                return $rs;
            }
        } else {
            $errors = $req->getAttribute('errors');
            $rs = $resp->withStatus(400)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode($errors));
            return $rs;
        }
    }

    /**
     * @api {post} http://api.commande.local:19080/commandes/ Créer une commande (non-authentifié).
     * @apiName insertCommand
     * @apiGroup Commandes
     * @apiExample {curl} Example usage:
     *     curl -X POST http://api.commande.local:19080/commandes/
     * @apiParam {String} nom le nom du client.
     * @apiParam {String} mail l'adresse mail du client.
     * @apiParam {Array} livraison Date et heure de la livraison.
     * @apiParam {Array} items uri et quantite des items souhaite.
     * @apiParamExample {json} Request-Example:
     *     {
     *      "nom" : "test",
     *      "mail": "test@gmail.fr",
     *      "livraison" : {
     *              "date": "7-12-2020",
     *              "heure": "12:30"
     *      },
     *      "items" : [
     *          { "uri": "/sandwichs/s19003", "q": 1}
     *          ]
     * }
     * @apiSuccess {ressource} commande  retourne les informations de la commande , le montant total et un token pour y acceder.
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *          "commande": {
     *              "nom": "test",
     *              "mail": "test@gmail.fr",
     *              "livraison": "2020-03-24 12:07:08"
     *      },
     *      "id": "73011897-6bf1-4231-a021-e78bcd62ddbc",
     *      "token": "672259ebfe53eda2153d4302f6063b8dc3fc4b7fa1d09f89791bee1994b7bd23",
     *      "montant": 15.75,
     *      "items": [
     *          {
     *              "uri": "/sandwichs/s19005",
     *              "q": 3
     *          }
     *      ]
     * }
     */
    public function insertCommand(Request $req, Response $resp, array $args)
    {
        if (!$req->getAttribute('has_errors')) {
            $getBody = $req->getBody();
            $json = json_decode($getBody, true);
            $client_mail = $json["mail"];
            $client_nom = $json["nom"];
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
            $commande_test->save();
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
            $errors = $req->getAttribute('errors');
            $rs = $resp->withStatus(400)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode($errors));
            return $rs;
        }
    }
}
