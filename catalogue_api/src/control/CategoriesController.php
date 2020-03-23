<?php

namespace lbs\command\control;

use Doctrine\Instantiator\Exception\ExceptionInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use MongoDB\Driver\WriteError;
use phpDocumentor\Reflection\Types\Integer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use \lbs\command\model\Commande as commande;
use function GuzzleHttp\Psr7\str;

class CategoriesController
{
    protected $c;

    public function __construct(\Slim\Container $c = null)
    {
        $this->c = $c;
    }

    /**
     * @api {get} http://api.catalogue.local:19180/categories/{id}/{sandwichs} Obtenir la liste des catégories de sandwichs
     * @apiName getCategorieSandwich
     * @apiGroup Catégories
     *
     * @apiParam {Number} id Categorie unique ID.
     *
     * @apiSuccess {Array} categorie Collection des catégories.
     * @apiSuccess {Array} sandwich  Collection des sandwichs.
     */
    public function getCategorieSandwich(Request $req, Response $resp, array $args)
    {
        $c = new \MongoDB\Client("mongodb://dbcat");
        $id = (int)$args["id"];
        $categories = $c->mongo->categories->find(["id" => $id]);
        foreach ($categories as $category) {
            $order = array();
            $order["categorie"]["id"] = $category->id;
            $dede = $order["categorie"]["nom"] = $category->nom;
            $order["categorie"]["description"] = $category->description;
            $orders["commandes"][] = $order;
        }
        $sandwichs = $c->mongo->sandwichs->find(["categories" => $order["categorie"]["nom"]]);
        $count = $c->mongo->sandwichs->count(["categories" => $order["categorie"]["nom"]]);
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
    }

    /**
     * @api {get} http://api.catalogue.local:19180/categories/{id} Description d'une catégorie
     * @apiName getCategorie
     * @apiGroup Catégories
     *
     * @apiParam {Number} id Categorie unique ID.
     *
     * @apiSuccess {Array} categorie Description de la catégorie.
     * @apiSuccess {Array} links  Liens vers la catégorie ou les sandwichs.
     */
    public function getCategorie(Request $req, Response $resp, array $args)
    {
        $c = new \MongoDB\Client("mongodb://dbcat");
        $id = (int)$args["id"];
        $categories = $c->mongo->categories->find(["id" => $id]);
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

    /**
     * @api {get} http://api.catalogue.local:19180/sandwich/{uri} Description d'un sandwich
     * @apiName getNameSandwich
     * @apiGroup Sandwichs
     *
     * @apiParam {String} uri Lien vers le sandwich.
     *
     * @apiSuccess {Array} sandwich Description d'un sandwich.
     *
     */
    public function getNameSandwich(Request $req, Response $resp, array $args)
    {
        $c = new \MongoDB\Client("mongodb://dbcat");
        $uri = (string)$args["uri"];
        $sandwichs = $c->mongo->sandwichs->find(["ref" => $uri]);
        foreach ($sandwichs as $sandwich) {
            $order = array();
            $order["ref"] = "/sandwichs/" . $sandwich->ref;
            $order["nom"] = $sandwich->nom;
            $order["prix"] = (string)$sandwich->prix;
        }
        $rs = $resp->withStatus(200)
            ->withHeader('Content-Type', 'application/json;charset=utf-8');
        $rs->getBody()->write(json_encode([$order]));
        return $rs;
    }

    /**
     * @api {get} http://api.catalogue.local:19180/categories Obtenir toutes les catégories
     * @apiName getCategories
     * @apiGroup Catégories
     *
     *
     * @apiSuccess {Array} categorie Liste des catégories.
     */
    public function getCategories(Request $req, Response $resp, array $args)
    {
        $c = new \MongoDB\Client("mongodb://dbcat");
        $categories = $c->mongo->categories->find();
        foreach ($categories as $category) {
            $order = array();
            $order["categories"]["id"] = $category->id;
            $order["categories"]["nom"] = $category->nom;
            $order["categories"]["description"] = $category->description;
            $orders["sandwich"][] = $order;
        }
        $rs = $resp->withStatus(200)
            ->withHeader('Content-Type', 'application/json;charset=utf-8');
        $rs->getBody()->write(json_encode($orders["sandwich"]));
        return $rs;
    }

    /**
     * @api {get} http://api.catalogue.local:19180/sandwichs Obtenir tous les sandwichs
     * @apiName getSandwichs
     * @apiGroup Sandwichs
     *
     *
     * @apiSuccess {Array} sandwichs Liste des sandwichs.
     */
    public function getSandwichs(Request $req, Response $resp, array $args)
    {
        $c = new \MongoDB\Client("mongodb://dbcat");
        $sandwichs = $c->mongo->sandwichs->find();
        foreach ($sandwichs as $sandwich) {
            $order = array();
            $order["sandwichs"]["ref"] = $sandwich->ref;
            $order["sandwichs"]["nom"] = $sandwich->nom;
            $order["sandwichs"]["description"] = $sandwich->description;
            $order["sandwichs"]["type_pain"] = $sandwich->type_pain;
            $orders["sandwichs"][] = $order;
        }
        $rs = $resp->withStatus(200)
            ->withHeader('Content-Type', 'application/json;charset=utf-8');
        $rs->getBody()->write(json_encode($orders["sandwichs"]));
        return $rs;
    }
}
