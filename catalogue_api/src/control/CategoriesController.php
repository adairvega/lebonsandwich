<?php

namespace lbs\command\control;

use Doctrine\Instantiator\Exception\ExceptionInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use MongoDB\Driver\WriteError;
use phpDocumentor\Reflection\Types\Integer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use \lbs\command\model\Commande as commande;

class CategoriesController
{
    protected $c;

    public function __construct(\Slim\Container $c = null)
    {
        $this->c = $c;
    }

    public function getCategorieSandwich(Request $req, Response $resp, array $args)
    {
        try{
        $c = new \MongoDB\Client("mongodb://dbcat");
        $id = (int)$args["id"];
        $categories = $c->catalogue->categories->find(["id" => $id]);
        foreach ($categories as $category) {
            $order = array();
            $order["categorie"]["id"] = $category->id;
            $dede = $order["categorie"]["nom"] = $category->nom;
            $order["categorie"]["description"] = $category->description;
            $orders["commandes"][] = $order;
        }
        $sandwichs = $c->catalogue->sandwichs->find(["categories" => $order["categorie"]["nom"]]);
        $count = $c->catalogue->sandwichs->count(["categories" => $order["categorie"]["nom"]]);
        foreach ($sandwichs as $sandwich) {
            $order = array();
            $order["sandwich"]["ref"] = $sandwich->ref;
            $order["sandwich"]["nom"] = $sandwich->nom;
            $order["sandwich"]["description"] = $sandwich->description;
            $order["sandwich"]["type_pain"] = $sandwich->type_pain;
            //$order["sandwich"]["categories"] = $sandwich->categories;  ??? do we need to print this value in the sandwichs collection
            $orders["sandwich"][] = $order;
        }
        $rs = $resp->withStatus(200)
            ->withHeader('Content-Type', 'application/json;charset=utf-8');
        $rs->getBody()->write(json_encode([
            "type" => "collection",
            "count" => $count,
            "size" => $count,
            "categorie" => $dede,
            "sandwichs" => $orders["sandwich"]]));
        return $rs;
        } catch (Exception $e) {
            return Writer::json_error($rs, 404, $e->getMessage());
        }
    }


    public function getCategorie(Request $req, Response $resp, array $args)
    {
        $c = new \MongoDB\Client("mongodb://dbcat");
        $id = (int)$args["id"];
        $categories = $c->catalogue->categories->find(["id" => $id]);
        foreach ($categories as $category) {
            $order = array();
            $order["id"] = $category->id;
            $order["nom"] = $category->nom;
            $order["description"] = $category->description;
        }
        $links = array(
            "sandwichs" => array(
                "href" => "http://api.catalogue.local:19180/categories/" . $id . "/sandwich/",
            ),
            "self" => array(
                "href" => "http://api.catalogue.local:19180/categories/" . $id . "/",
            )
        );
        $rs = $resp->withStatus(200)
            ->withHeader('Content-Type', 'application/json;charset=utf-8');
        $rs->getBody()->write(json_encode([
            "type" => "resource",
            "date" => date("Y-m-d"),
            "categorie" => $order,
            "links" => $links]));
        return $rs;
    }
}
