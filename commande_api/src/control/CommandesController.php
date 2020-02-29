<?php

namespace lbs\command\control;

use Firebase\JWT\JWT;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use lbs\command\model\Item as item;
use lbs\command\model\Client as user;
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
        try {
            $links = array(
                "self" => "http://api.checkcommande.local:19280/commandes/?page=",

                "items" => "http://api.checkcommande.local:19280/commandes/?page="
            );
            $id = $args['id'];
            $cde = Commande::findOrFail($id);
            $items_commande = $cde->getItems()->get();
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
                $client = new Client([
                    "base_uri" => "http://api.catalogue.local"
                ]);
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
                $commande_test->id = Uuid::uuid4();
                $token = random_bytes(32);
                $token = bin2hex($token);
                $commande_test->nom = (filter_var($args['nom'], FILTER_SANITIZE_STRING));
                $commande_test->livraison = date("Y-m-d h:i:s");
                $commande_test->mail = filter_var($args['mail'], FILTER_VALIDATE_EMAIL);
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
        }
    }

    public function userAuthentication(Request $req, Response $resp, array $args)
    {
        // maybe we can do the check of the Authorization Header inside a MiddleWare ?
        if (!empty($req->getHeader('Authorization')[0])) {
            $getHeader = $req->getHeader('Authorization')[0];
            $getHeader_value = substr($getHeader, 6);
            $getHeader_value_decode = base64_decode($getHeader_value);
            $dote_position = strpos($getHeader_value_decode, ':');
            $user_name = substr($getHeader_value_decode, 0, $dote_position);
            $user_passwd = substr($getHeader_value_decode, $dote_position + 1);
            $user = new user();
            $user = user::where('mail_client', '=', $user_name)->where('passwd', '=', $user_passwd)->get();
            if (!$user->isEmpty()) {
                $token = JWT::encode(
                    ['iss' => 'http://api.commande.local:19080',
                        'aud' => 'http://api.commande.local:19080',
                        'iat' => time(),
                        'exp' => time() + 3600,
                        'uname' => $user_name,
                        'lvl' => 1],
                    'HS512');
                $rs = $resp->withStatus(200)
                    ->withHeader('Content-Type', 'application/json;charset=utf-8');
                $rs->getBody()->write(json_encode([
                    "token" => $token
                ]));
                return $rs;
            } else {
                $rs = $resp->withStatus(401)
                    ->withHeader('Content-Type', 'application/json;charset=utf-8');
                $rs->getBody()->write(json_encode(['type' => 'error', 'Error_code' => 401, 'message :' => 'email or password incorrect']));
                return $rs;
            }
        } else {
            $rs = $resp->withStatus(401)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode(['type' => 'error', 'Error_code' => 401, 'message :' => 'no authorization header present']));
            return $rs;
        }
    }
}
