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

    /**
     * @api {get} http://api.checkcommande.local:19280/commandes/{id} Obtenir la description détaillée d'une commande.
     * @apiName getCommand
     * @apiGroup Commandes
     *
     * @apiParam {Number} id id unique de la commande.
     *
     * @apiExample {curl} Example usage:
     *     curl http://api.checkcommande.local:19280/commandes/cdf6302b-940b-4348-b913-3cb2052bf042
     *
     * @apiSuccess {ressource} Collection description détaillée d'une commande.
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *
     * {
     *   "type": "resource",
     *   "links": {
     *      "self": "http://api.checkcommande.local:19280/commandes/cdf6302b-940b-4348-b913-3cb2052bf042",
     *      "items": "http://api.checkcommande.local:19280/commandes/cdf6302b-940b-4348-b913-3cb2052bf042/items"
     * },
     *  "command": {
     *      "id": "cdf6302b-940b-4348-b913-3cb2052bf042",
     *      "created_at": "2019-11-08T13:45:55.000000Z",
     *      "livraison": "2019-11-08 16:56:15",
     *      "nom": "Dubois",
     *      "mail": "Dubois@free.fr",
     *      "montant": "40.25",
     *      "items": [
     *          {
     *              "uri": "/sandwichs/s19005",
     *              "libelle": "la mer",
     *              "tarif": "5.25",
     *              "quantite": 2
     *          }
     *      ]
     *    }
     *  }
     */
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
                "items" => "http://api.checkcommande.local:19280/commandes/" . $id . "/items"
            );
            $rs = $resp->withStatus(200)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode([
                "type" => "resource",
                "links" => $links,
                "command" => $order]));
            return $rs;
        } catch (ModelNotFoundException $e) {
            $rs = $resp->withStatus(400)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode(['Error_code' => 400, 'Error message' => $e->getMessage()]));
            return $rs;
        }
    }

    /**
     * @api {put} http://api.checkcommande.local:19280/commandes/{id} Modifier l'état d'une commande.
     * @apiName updateCommand
     * @apiGroup Commandes
     * @apiExample {curl} Example usage:
     *     curl -X PUT http://api.checkcommande.local:19280/commandes/d9b0aef8-a42d-439d-a7d3-fa5672d1e1f1/
     *
     * @apiParam {Number} Id id de la commande a change.
     * @apiParamExample {json} Request-Example:
     *  {
     *     "status" : 2
     *   }
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "id": "d9b0aef8-a42d-439d-a7d3-fa5672d1e1f1",
     *       "created_at": "2020-03-23 22:08:41",
     *       "updated_at": "2020-03-23 22:54:21",
     *       "livraison": "2020-03-23 10:08:41",
     *       "nom": "jojo",
     *       "mail": "jojo@gmail.fr",
     *       "montant": "157.50",
     *       "remise": null,
     *       "token": "fbaf3a6d7385b3019b82f024b1882ead1dd15fd0a34ad404ce0aa07cb9cd44a8",
     *       "client_id": null,
     *       "ref_paiement": null,
     *       "date_paiement": null,
     *       "mode_paiement": null,
     *       "status": 2
     *    }
     */
    public function updateCommand(Request $req, Response $resp, array $args)
    {
        if ($commande = commande::find($args["id"])) {
            $getBody = $req->getBody();
            $body = json_decode($getBody, true);
            if (!empty($body["status"])) {
                if ($body["status"] > 0 and $body["status"] <= 4) {
                    $commande->status = $body["status"];
                    $commande->save();
                    $rs = $resp->withStatus(200)
                        ->withHeader('Content-Type', 'application/json;charset=utf-8');
                    $rs->getBody()->write(json_encode($commande));
                    return $rs;
                } else {
                    $res = $resp->withStatus(400)
                        ->withHeader('Content-Type', 'application/json;charset=utf-8');
                    $res->getBody()->write(json_encode(['Error_code' => 400, 'status value expected to be between 1 and 4']));
                    return $res;
                }
            } else {
                $rs = $resp->withStatus(400)
                    ->withHeader('Content-Type', 'application/json;charset=utf-8');
                $rs->getBody()->write(json_encode(['Error_code' => 400, 'please send a status']));
                return $rs;
            }
        } else {
            $rs = $resp->withStatus(400)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode(['Error_code' => 400, 'no command existing with this id']));
            return $rs;
        }
    }

    /**
     * @api {get} http://api.checkcommande.local:19280/commandes/ Obtenir la listes de toutes les commandes.
     * @apiName getCommands
     * @apiGroup Commandes
     *
     * @apiExample {curl} Example usage:
     *     curl http://api.checkcommande.local:19280/commandes/
     *
     * @apiSuccess {Collection} Collection Description et sandwichs d'une categorie.
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     * {
     *   "type": "collection",
     *   "count": 1515,
     *   "size": 10,
     *   "links": 0,
     *   "categorie": "veggie",
     *    commandes": [
     *      {
     *        "commande": {
     *           "id": "06d23c7f-3a7d-4499-b7f1-0bb53ae40495",
     *           "nom": "Collet",
     *           "created_at": "2019-11-08T13:45:56.000000Z",
     *           "livraison": "2019-11-09 13:06:06",
     *           "status": 1
     *       },
     *       "links": {
     *           "self": {
     *                  "href": "http://api.checkcommande.local:19280/commandes/06d23c7f-3a7d-4499-b7f1-0bb53ae40495"
     *           }
     *        }
     *     }
     *   ]
     * }
     */
    public function getCommands(Request $req, Response $resp, array $args)
    {
        try {
            $size = 0;
            $links = 0;
            $url = $_SERVER['REQUEST_URI'];
            $parts = parse_url($url);
            if (sizeof($parts) > 1) {
                parse_str($parts['query'], $query);
                $uri_key = (array_keys($query));
                if (sizeof($uri_key) <= 1) {
                    if ($uri_key[0] == 'page') {
                        $page_skip = (int)$query[$uri_key[0]] * 10 - 10;
                        $count = commande::all()->count();
                        $size = 10;
                        $cde = commande::all()->skip($page_skip)->take((int)$size);
                    } else {
                        $count = commande::where($uri_key[0], '=', $query[$uri_key[0]])->count();
                        if ($count < 10) {
                            $size = $count;
                        } else {
                            $size = 10;
                        }
                        $cde = commande::where($uri_key[0], '=', $query[$uri_key[0]])->take(10)->get();
                    }
                } elseif ($uri_key[0] == 'page' && $uri_key[1] == 'size') {
                    $page_skip = (int)$query[$uri_key[0]] * 10 - 10;
                    $size = (int)$query[$uri_key[1]];
                    $count = $total_commandes = commande::all()->count();
                    $total_pages = $total_commandes / $size;
                    if ($page_skip > $total_pages) {
                        $cde = commande::latest()->take($size)->get();
                    } else {
                        $cde = commande::skip($page_skip)->take($size)->get();
                    }
                    $page = (int)$query[$uri_key[0]];
                    $links = array(
                        "next" => array(
                            "href" => "http://api.checkcommande.local:19280/commandes?page=" . ($page + 1) . "&size=" . $size,
                        ),
                        "prev" => array(
                            "href" => "http://api.checkcommande.local:19280/commandes?page=" . ($page - 1) . "&size=" . $size,
                        ),
                        "last" => array(
                            "href" => "http://api.checkcommande.local:19280/commandes?page=" . round($total_pages) . "&size=" . $size,
                        ),
                        "first" => array(
                            "href" => "http://api.checkcommande.local:19280/commandes?page=1&size=" . $size,
                        )
                    );
                } else {
                    $page = array_search("page", $uri_key);
                    $data = $uri_key[!"page"];
                    $data_position = array_search($data, $uri_key);
                    $page_skip = (int)$query[$uri_key[$page]] * 10 - 10;
                    $count = commande::where($data, '=', $query[$uri_key[$data_position]])->count();
                    if ($count < 10) {
                        $size = $count;
                    } else {
                        $size = 10;
                    }
                    $cde = commande::where($data, '=', $query[$uri_key[$data_position]])->skip($page_skip)->take(10)->get();
                }
            } else {
                $count = commande::all()->count();
                $size = 10;
                $cde = commande::all()->take(10);
            }
            $orders["commandes"] = array();
            foreach ($cde as $commande) {
                $order = array();
                $order["commande"]["id"] = $commande->id;
                $order["commande"]["nom"] = $commande->nom;
                $order["commande"]["created_at"] = $commande->created_at;
                $order["commande"]["livraison"] = $commande->livraison;
                $order["commande"]["status"] = $commande->status;
                $order["links"]["self"] = array("href" => "http://api.checkcommande.local:19280/commandes/" . $commande->id);
                $orders["commandes"][] = $order;
            }
            $rs = $resp->withStatus(200)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode([
                "type" => "collection",
                "count" => $count,
                "size" => $size,
                "links" => $links,
                "commandes" => $orders["commandes"]]));
            return $rs;
        } catch (Exception $e) {
            return Writer::json_error($rs, 400, $e->getMessage());
        }
    }

    /**
     * @api {get} http://api.checkcommande.local:19280/commandes/{id}/items Obtenir les items d'une commande.
     * @apiName getItems
     * @apiGroup Items
     *
     * @apiExample {curl} Example usage:
     *     curl http://api.checkcommande.local:19280/commandes/d9b0aef8-a42d-439d-a7d3-fa5672d1e1f1/items
     * @apiParam {Number} id id unique de la commande.
     * @apiSuccess {item} items les items d'une commande.
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *
     * {
     *   "type": "item",
     *   "id": "d9b0aef8-a42d-439d-a7d3-fa5672d1e1f1"
     *   "items": [
     *       {
     *          "id": 6796,
     *          "uri": "/sandwichs/s19005",
     *          "libelle": "la mer",
     *          "tarif": "5.25",
     *          "quantite": 30,
     *          "commande_id": "d9b0aef8-a42d-439d-a7d3-fa5672d1e1f1"
     *          }
     *       ]
     *    }
     */
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
            $rs = $resp->withStatus(400)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode(['Error_code' => 400, 'Error message' => $e->getMessage()]));
            return $rs;
        }
    }
}
