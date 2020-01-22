<?php

namespace lbs\command\control;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use MongoDB\Driver\WriteError;
use phpDocumentor\Reflection\Types\Integer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

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
            $url = $_SERVER['REQUEST_URI'];
            $parts = parse_url($url);
            if (sizeof($parts) > 1) {
                parse_str($parts['query'], $query);
                $uri_key = (array_keys($query));
                if (sizeof($uri_key) <= 1) {
                    if ($uri_key[0] == 'page') {
                        $page_skip = (int)$query[$uri_key[0]] * 10 - 10;
                        $cde = commande::all();
                        $count = count($cde);
                        $cde = commande::all()->skip($page_skip)->take(10);
                    } else {
                        $cde = commande::where($uri_key[0], '=', $query[$uri_key[0]])->get();
                        $count = count($cde);
                        $cde = commande::where($uri_key[0], '=', $query[$uri_key[0]])->take(10)->get();
                    }
                } else {
                    $page = array_search("page", $uri_key);
                    $data = $uri_key[!"page"];
                    $data_position = array_search($data, $uri_key);
                    $page_skip = (int)$query[$uri_key[$page]] * 10 - 10;
                    $cde = commande::where($data, '=', $query[$uri_key[$data_position]])->get();
                    $count = count($cde);
                    $cde = commande::where($data, '=', $query[$uri_key[$data_position]])->skip($page_skip)->take(10)->get();
                }
            } else {
                $cde = commande::all();
                $count = count($cde);
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
=======

            $cde = \lbs\command\model\Commande::select(['id', 'nom', 'created_at','livraison', 'status'])->get();
            $cde_count = \lbs\command\model\Commande::all()->count();

            $rows = $cde->orderBy('livraison')->get();
            $commands = [];

            foreach($rows as $row){
                $commands[] = [
                    'command' => $row->toArray(),
                    'links' => [
                        'self' => [
                            'href' => $this->c->get('router')
                                                ->pathFor('command', ['id'=>$row->id])]]]
            }

>>>>>>> 94485259d4ee1f3519b7e1ab59392fdffd78b2f1
            $rs = $resp->withStatus(200)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode([
                "type" => "collection",
<<<<<<< HEAD
                "count" => $count,
                "size" => 10,
                "commandes" => $orders["commandes"]]));
=======
                "count" => $cde_count,
                "commands"=> $cde->toArray()]));

>>>>>>> 94485259d4ee1f3519b7e1ab59392fdffd78b2f1
            return $rs;
        } catch (\Exception $e) {
            return Writer::json_error($rs, 404, $e->getMessage());
        }
    }

    public function getCommand(Request $req, Response $resp, array $args)
    {
        try {
            $id = $args['id'];

            $cde = \lbs\command\model\Commande::findOrFail($id);

            $rs = $resp->withStatus(200)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');

            $rs->getBody()->write(json_encode([
                "type" => "collection",
                "commandes" => $cde]));

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
        }
    }
}
