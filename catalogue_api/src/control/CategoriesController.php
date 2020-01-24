<?php

namespace lbs\command\control;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use MongoDB\Driver\WriteError;
use phpDocumentor\Reflection\Types\Integer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CategoriesController
{
    protected $c;

    public function __construct(\Slim\Container $c = null)
    {
        $this->c = $c;
    }

    public function getCategories(Request $req, Response $resp, array $args)
    {
        try{

            $connection = new MongoDB\Client("mongodb://dbcat:27017");
            $db = $connection->catalogue;

            $cat = $db->categories;
            $sand = $db->sandwichs;

            $col = $cat->find([]);
            $colsand = $sand->find([]);

            $categories["categories"] = array();
            foreach ($col as $categ) {
                $categorie = array();
                $categorie["categories"]["id"] = $categ->id;
                $categorie["categories"]["nom"] = $categ->nom;
                $categorie["categories"]["description"] = $categ->description;
                $categories["categories"][] = $categorie;
            }

            $rs = $resp->withStatus(200)
                        ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode((
                "type": "ressource",
                "date": "29-10-2018",
                "categorie": $categories["categories"])));

        }catch(\Exception $e){
            return Writer::json_error($rs, 404, $e->getMessage());
        }
    }
}