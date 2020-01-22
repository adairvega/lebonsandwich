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
        $connection = new MongoDB\Client("mongodb://dbcat:27017");
        $db = $connection->catalogue;
        $cat = $db->categories;
        $col = $cat->find([]);
        foreach ($col as $categories) {
        echo $categories["nom"] . "\n";
        }
}