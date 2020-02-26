<?php

namespace lbs\command\control;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use lbs\command\Item;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use \lbs\command\model\Commande as commande;
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
<<<<<<< HEAD
            $cde = commande::all();
=======
            $cde = \lbs\command\model\Commande::all();

>>>>>>> c3c34b4d701bc0e258fdd584775a3af74150a69a
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
        try {
            $id = $args['id'];
<<<<<<< HEAD
            $cde = \lbs\command\model\Commande::findOrFail($id);
            $rs = $resp->withStatus(200)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode([
                "type" => "collection",
                "commandes" => $cde]));
=======
            $cde = Commande::findOrFail($id);
            $order = array();
            $order["nom"] = $cde->nom;
            $order["mail"] = $cde->mail;
            $order["livraison"] = array("date" => $cde->livraison, "heure" => $cde->livraison);
            $order["id"] = $cde->id;
            $order["token"] = $cde->token;
            $order["montant"] = $cde->montant;
            $rs = $resp->withStatus(200)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode([
                "commande" => $order]));
>>>>>>> c3c34b4d701bc0e258fdd584775a3af74150a69a
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
            $client = new Client([
                "base_uri" => "http://api.catalogue.local"
            ]);
            $request = $client->get('/categories/2/sandwichs');
            echo $response = $request->getBody();
            die();
            $dede = json_decode($req->getBody());
            foreach ($dede->items as $item) {
                $q = (int)$item->q;
                $response = $client->request('GET', '/sandwich/' + $item->uri);
                $sandwich = json_decode($response->getBody());
                foreach ($sandwich as $value) {
                    $order[] = array();
                    $order['uri'] = $value->ref;
                    $order['nom'] = $value->nom;
                    $order['prix'] = (float)$value->prix;
                    $order['quantite'] = $q;
                }
            }
            die("dedede");
            if (filter_var($args['mail'], FILTER_VALIDATE_EMAIL) == !0) {
                $commande_test = new commande();
                $commande_test->id = Uuid::uuid4();
                $token = random_bytes(32);
                $token = bin2hex($token);
                $commande_test->nom = (filter_var($args['nom'], FILTER_SANITIZE_STRING));
                $commande_test->livraison = date("Y-m-d h:i:s");
                $commande_test->mail = filter_var($args['mail'], FILTER_VALIDATE_EMAIL);
                $commande_test->token = $token;
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
<<<<<<< HEAD
=======

>>>>>>> c3c34b4d701bc0e258fdd584775a3af74150a69a
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