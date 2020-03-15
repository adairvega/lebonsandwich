<?php

namespace lbs\command\control;

use Firebase\JWT\JWT;
use lbs\command\model\Client as user;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserController
{
    protected $c;

    public function __construct(\Slim\Container $c = null)
    {
        $this->c = $c;
    }

    public function userAuthentication(Request $req, Response $resp, array $args)
    {
        $user_name = $req->getAttribute("user_name");
        $user_passwd = $req->getAttribute("user_passwd");
        $user = new user();
        $users = user::where('mail_client', '=', $user_name)->where('passwd', '=', $user_passwd)->get();
        foreach ($users as $user) {
            $user_id = $user->id;
        }
        if (!$users->isEmpty()) {
            $token = JWT::encode(
                ['iss' => 'http://api.commande.local',
                    'aud' => 'http://api.commande.local',
                    'iat' => time(),
                    'exp' => time() + 3600,
                    'uid' => $user_id,
                    'lvl' => 1],
                "secret", 'HS512');
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
    }


    public function userProfile(Request $req, Response $resp, array $args)
    {
        $token = $req->getAttribute("token");
        $user_id = $args["user_id"];
        if ($user_id == $token->uid) {
            $user = new user();
            $user = user::find($token->uid);
            $user_mail = $user->mail_client;
            $user_cumul = $user->cumul_achats;
            $rs = $resp->withStatus(200)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode(["user email" => $user_mail, "user cumul achats" => $user_cumul]));
            return $rs;
        } else {
            $rs = $resp->withStatus(401)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode(["user and token no corresponding"]));
            return $rs;
        }
    }

}