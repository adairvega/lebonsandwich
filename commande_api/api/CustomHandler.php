<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class CustomHandler extends \Slim\Handlers\Error {
    public function __invoke(Request $request, Response $response, \Exception $exception){
        return $response
            ->withStatus(400)
            ->withHeader('Content-Type', 'application/json;charset=utf-8')
            ->write('Something went wrong!');
    }
}
