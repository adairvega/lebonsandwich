<?php

namespace lbs\command\control;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CommandesController {
	protected $c;

	public function __construct( \Slim\Container $c = null){
		$this->c = $c;
	}

	public function getCommands(Request $req, Response $resp, array $args) : Response {

		$cde = \lbs\command\model\Commande::all();

		$rs = $resp->withStatus(200)
					->withHeader('Content-Type', 'application/json;charset=utf-8');

		$rs->getBody()->write(json_encode([
					"type" => "collection",
					"count" => $cde,
					"commandes" => $cde]));

		return $rs;
	}

	public function getCommand(Request $req, Response $resp, array $args) : Response {

		/*$cde = [
			["id" => "AuTR4-65ZTY", "mail_client" => "jan.neymar@yaboo.fr", "date_commande" => "2019-12-25", "montant" => 25.95
						],
			["id" => "657GT-I8G443", "mail_client" => "jan.neplin@gmal.fr", "date_commande" => "2019-11-27", "montant" => 42.95
						],
			["id" => "K9J67-4D6F5", "mail_client" => "claude.francois@grorange.fr", "date_commande" => "2019-12-07", "montant" => 14.95
			]
		];*/

		$cde = \lbs\command\model\Commande::first();

		$rs = $resp->withStatus(200)
					->withHeader('Content-Type', 'application/json;charset=utf-8');

		$rs->getBody()->write(json_encode([
					"type" => "collection",
					"count" => $cde,
					"commandes" => $cde]));

		return $rs;
	}
}
