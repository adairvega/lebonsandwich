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
     * @api {get} http://api.catalogue.local:19180/categories/{id}/sandwichs Obtenir la liste des sandwichs d'une catégorie.
     * @apiName getCategorieSandwich
     * @apiGroup Catégories
     *
     * @apiExample {curl} Example usage:
     *     curl http://api.catalogue.local:19180/categories/1/sandwichs
     *
     * @apiParam {Numero} id numero de la categorie.
     *
     * @apiSuccess {Collection} Collection Description et sandwichs d'une categorie.
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     * {
     *   "type": "collection",
     *   "count": 1,
     *   "size": 1,
     *   "categorie": "veggie",
     *   "sandwichs": [
     *       {
     *          "sandwich": {
     *              "ref": "s19007",
     *              "nom": "le club sandwich",
     *              "description": "le club sandwich comme à Saratoga : pain toasté, filet de dinde, bacon, laitue, tomate",
     *              "type_pain": "mie"
     *              }
     *          }
     *      ]
     *  }
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
     * @api {get} http://api.catalogue.local:19180/categories/{id} Obtenir la description d'une catégorie.
     * @apiName getCategorie
     * @apiGroup Catégories
     * @apiExample {curl} Example usage:
     *     curl http://api.catalogue.local:19180/categories/1
     *
     * @apiParam {Numero} id numero de la categorie.
     *
     * @apiSuccess {resource} categorie Description d'une categorie.
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     * {
     *
     *   "type": "resource",
     *   "date": "2020-03-23",
     *   "categorie": {
     *       "id": 1,
     *       "nom": "traditionnel",
     *       "description": "nos sandwiches et boissons classiques"
     * },
     * "links": {
     *     "sandwichs": {
     *          "href": "http://api.catalogue.local:19180/categories/1/sandwichs/"
     *      },
     *      "self": {
     *      "href": "http://api.catalogue.local:19180/categories/1/"
     *          }
     *      }
     * }
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
                "href" => "http://api.catalogue.local:19180/categories/" . $id . "/sandwichs/",
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
     * @api {get} http://api.catalogue.local:19180/sandwichs/{uri} Obtenir la description d'un sandwich.
     * @apiName getNameSandwich
     * @apiGroup Sandwichs
     * @apiExample {curl} Example usage:
     *     curl http://api.catalogue.local:19180/sandwichs/s19002
     *
     * @apiParam {String} uri numero de reference du sandwich.
     *
     * @apiSuccess {JsonArray} sandwich Description d'un sandwich.
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *      {
     *          "ref": "/sandwichs/s19002",
     *          "nom": "le jambon-beurre",
     *          "prix": "4.50"
     *      }
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
     * @api {get} http://api.catalogue.local:19180/categories Obtenir la liste des catégories de sandwich.
     * @apiName getCategories
     * @apiGroup Catégories
     * @apiExample {curl} Example usage:
     *     curl http://api.catalogue.local:19180/categories
     *
     * @apiSuccess {JsonArray} categories Tableau de toutes les categories.
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     * [
     *  {
     *      "categories": {
     *          "id": 4,
     *          "nom": "world",
     *          "description": "nos produits du monde, à découvrir !"
     *          }
     *  },
     *  {
     *      "categories": {
     *      "id": 2,
     *      "nom": "chaud",
     *      "description": "nos sandwiches et boissons chaudes"
     *          }
     *  }
     * ]
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
     * @api {get} http://api.catalogue.local:19180/sandwichs Obtenir la liste des sandwichs du catalogue.
     * @apiName getSandwichs
     * @apiGroup Sandwichs
     * @apiExample {curl} Example usage:
     *     curl http://api.catalogue.local:19180/sandwichs
     *
     * @apiSuccess {JsonArray} sandwichs tableau de tous les sandwichs.
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *      [
     *          {
     *              "sandwichs": {
     *                  "ref": "s19001",
     *                  "nom": "le bucheron",
     *                  "description": "un sandwich de bucheron : frites, fromage, saucisse, steack, lard grillés, mayo",
     *                  "type_pain": "baguette"
     *              }
     *          },
     *          {
     *              "sandwichs": {
     *                  "ref": "s19002",
     *                  "nom": "le jambon-beurre",
     *                  "description": "le jambon-beurre traditionnel, avec des cornichons",
     *                  "type_pain": "baguette"
     *              }
     *          }
     *      ]
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
