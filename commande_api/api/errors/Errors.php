<?php

namespace lbs\command\api\errors;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Errors {

	protected $c;

	public function __construct( \Slim\Container $c = null){
		$this->c = $c;
	}

	public function BadUri(Request $req, Response $resp){
		$resp = $resp->withStatus(400);
		$resp->getBody()->write('URI non traitée');

		return $resp;
	}

	public function NotAllowed(Request $req, Response $resp, $methods){
		$resp = $resp->withStatus(405)
					->withHeader('Allow', implode(',', $methods));
		$resp->getBody()
			->write('méthode permises : ' . implode(',', $methods));

		return $resp;
	}

	public function Internal(Request $req, Response $resp, $error){
		$resp = $resp->withStatus(500);
		$resp->getBody()
			->write('error :' . $e->getMessage() )
			->write('file :' . $e->getFile() )
			->write('line :' . $e->getLine() );

		return $resp;
	}
}