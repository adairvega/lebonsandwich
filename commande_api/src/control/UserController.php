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

    /**
     * @api {get} http://api.commande.local:19080/client/{user_id} Obtenir les données du client.
     * @apiName userProfiles
     * @apiGroup User
     *
     * @apiHeader {Bearer_Token}  Authorization JWT du client connecte.
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpc3MiOiJodHRwOlwvXC9hcGkuYmFja29mZmljZS5sb2NhbCIsImF1ZCI6Imh0dHA6XC9cL2FwaS5iYWNrb2ZmaWNlLmxvY2FsIiwiaWF0IjoxNTg0OTY5NzE3LCJleHAiOjE1ODQ5NzMzMTcsInVpZCI6IjA0MDg5NmVlLTg4M2MtNDBlMi1iNDA1LWVkMGU3NzIyOTlhNCIsImx2bCI6MX0.nhmmDPn-iHWCDVQTNOd1vQXHUG2V9Jw6Uk5Ml3oxooUaRId2wd1Bru1O3WFoUDA9K6MEO_Xp3CGqO3COvGAujw"
     *     }
     * @apiExample {curl} Example usage:
     *     curl http://api.commande.local:19080/client/101
     * @apiParam {Number} user_id id du client.
     *
     * @apiSuccess {Json} client informations concernant le client connecté.
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "nom client": "pepe",
     *       "user email": "pepe@gmail.com",
     *       "user cumul achats": "315.00"
     *     }
     */
    public function userProfile(Request $req, Response $resp, array $args)
    {
        $token = $req->getAttribute("token");
        $user_id = $args["user_id"];
        if ($user_id == $token->uid) {
            $user = user::find($token->uid);
            $user_nom = $user->nom_client;
            $user_mail = $user->mail_client;
            $user_cumul = $user->cumul_achats;
            $rs = $resp->withStatus(200)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode(["nom client" => $user_nom, "user email" => $user_mail, "user cumul achats" => $user_cumul]));
            return $rs;
        } else {
            $rs = $resp->withStatus(400)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode(["user id and JWT no corresponding"]));
            return $rs;
        }
    }

    /**
     * @api {post} http://api.commande.local:19080/client/signup Creation d'un client.
     * @apiName userSignup
     * @apiGroup User
     * @apiExample {curl} Example usage:
     *     curl -X POST http://api.commande.local:19080/client/signup
     * @apiParam {String} nom_client Le prenom du client.
     * @apiParam {String} mail_client l'adresse mail du client.
     * @apiParam {String} passwd Le mot de passe.
     * @apiParamExample {json} Request-Example:
     *   {
     *     "nom_client": "test",
     *     "mail_client": "test@gmail.com",
     *     "passwd": "test"
     *   }
     * @apiSuccess {Json} Message confirme que le compte a bien été crée.
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "votre compte utilisateur a bien été crée."
     *     }
     */
    public function userSignup(Request $req, Response $resp, array $args)
    {
        if (!$req->getAttribute('errors')) {
            $getBody = $req->getBody();
            $json = json_decode($getBody, true);
            $user = new user();
            $user->nom_client = filter_var($json["nom_client"], FILTER_SANITIZE_STRING);
            $user->mail_client = filter_var($json["mail_client"], FILTER_SANITIZE_EMAIL);
            $user->passwd = password_hash($json["passwd"], PASSWORD_DEFAULT);
            $user->created_at = date("Y-m-d H:i:s");
            $user->updated_at = date("Y-m-d H:i:s");
            $user->save();
            $rs = $resp->withStatus(200)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode("votre compte utilisateur a bien été crée"));
            return $rs;
        } else {
            $errors = $req->getAttribute('errors');
            $rs = $resp->withStatus(400)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode($errors));
            return $rs;
        }
    }

    /**
     * @api {post} http://api.commande.local:19080/client/signin Connexion client.
     * @apiName userSignin
     * @apiGroup User
     * @apiExample {curl} Example usage:
     *     curl -X POST http://api.commande.local:19080/client/signin
     * @apiHeader {Basic_Auth}  Authorization email and password du client.
     * @apiParam {String} user_mail l'adresse mail du client.
     * @apiParam {String} user_passwd Le mot de passe.
     * @apiSuccess {Json} token retourne un JWT au client.
     * @apiParamExample {json} Request-Example:
     *     {
     *      "user_mail": "test@gmail.com",
     *       "user_passwd": "test"
     *      }
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "token" : "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpc3MiOiJodHRwOlwvXC9hcGkuYmFja29mZmljZS5sb2NhbCIsImF1ZCI6Imh0dHA6XC9cL2FwaS5iYWNrb2ZmaWNlLmxvY2FsIiwiaWF0IjoxNTg1MDA2MDU1LCJleHAiOjE1ODUwMDk2NTUsInVpZCI6MTAxLCJsdmwiOjF9.yvr5HKtcUIT2NQoWlMHQxU_ZSMjQ2cPlFnRL3ZUyWxmBBNQhIwQ_eqyS_wMVsOW_g9V__MqD2_Ydu4_Syg3CIg"
     *     }.
     */
    public function userSignin(Request $req, Response $resp, array $args)
    {
        $user_email = $req->getAttribute("user_email");
        $user_password = $req->getAttribute("user_passwd");
        if ($user = user::where('mail_client', '=', $user_email)->first()) {
            if (password_verify($user_password, $user->passwd)) {
                $token = JWT::encode(
                    ['iss' => 'http://api.backoffice.local',
                        'aud' => 'http://api.backoffice.local',
                        'iat' => time(),
                        'exp' => time() + 3600,
                        'uid' => $user->id,
                        'lvl' => 1],
                    "secret", 'HS512');
                $rs = $resp->withStatus(200)
                    ->withHeader('Content-Type', 'application/json;charset=utf-8');
                $rs->getBody()->write(json_encode([
                    "token" => $token
                ]));
                return $rs;
            } else {
                $rs = $resp->withStatus(400)
                    ->withHeader('Content-Type', 'application/json;charset=utf-8');
                $rs->getBody()->write(json_encode(['type' => 'error', 'Error_code' => 400, 'message :' => 'email ou mot de passe incorrect']));
                return $rs;
            }
        } else {
            echo "aucun compte ne correspond à cette adresse email.";
        }
    }

    /**
     * @api {get} http://api.commande.local:19080/client/{user_id}/commandes Obtenir l'historique des commandes d'un client.
     * @apiName userHistoric
     * @apiGroup User
     * @apiHeader {Bearer_Token}  Authorization JWT du client connecte.
     * @apiHeaderExample {json} Header-Example:
     *     {
     *       "Token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpc3MiOiJodHRwOlwvXC9hcGkuYmFja29mZmljZS5sb2NhbCIsImF1ZCI6Imh0dHA6XC9cL2FwaS5iYWNrb2ZmaWNlLmxvY2FsIiwiaWF0IjoxNTg0OTY5NzE3LCJleHAiOjE1ODQ5NzMzMTcsInVpZCI6IjA0MDg5NmVlLTg4M2MtNDBlMi1iNDA1LWVkMGU3NzIyOTlhNCIsImx2bCI6MX0.nhmmDPn-iHWCDVQTNOd1vQXHUG2V9Jw6Uk5Ml3oxooUaRId2wd1Bru1O3WFoUDA9K6MEO_Xp3CGqO3COvGAujw"
     *     }
     * @apiExample {curl} Example usage:
     *     curl http://api.commande.local:19080/client/101/commandes
     * @apiParam {Number} user_id id du client.
     *
     * @apiSuccess {Array} commandes Liste de toutes les commandes du client.
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *      "commandes": {
     *          "commandes": [
     *                {
     *                    "commande": {
     *                          "id": "b9536d63-465d-49ee-b8eb-5daf0e8c138c",
     *                          "token": "33980a6ada52bc731df0e66779bc8de8b24788a20821ab9f513800d19d213e64",
     *                          "status": 1,
     *                          "montant": "157.50",
     *                          "created_at": "2020-03-24T00:18:18.000000Z",
     *                          "livraison": "2020-03-24 12:18:18",
     *                          "mail_client": "jojo@gmail.fr",
     *                          "nom_client": "jojo"
     *                    }
     *               }
     *          ]
     *      }
     *   }
     */
    public function userHistoric(Request $req, Response $resp, array $args)
    {
        $user_id = $args["user_id"];
        $token = $req->getAttribute("token");
        if ($user_id == $token->uid) {
            $user = user::find($token->uid);
            $commandes = $user->getCommandes()->get();
            foreach ($commandes as $commande) {
                $order = array();
                $order["commande"]["id"] = $commande->id;
                $order["commande"]["token"] = $commande->token;
                $order["commande"]["status"] = $commande->status;
                $order["commande"]["montant"] = $commande->montant;
                $order["commande"]["created_at"] = $commande->created_at;
                $order["commande"]["livraison"] = $commande->livraison;
                $order["commande"]["mail_client"] = $commande->mail;
                $order["commande"]["nom_client"] = $commande->nom;
                $orders["commandes"][] = $order;
            }
            $rs = $resp->withStatus(200)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode([
                "commandes" => $orders]));
            return $rs;
        } else {
            $rs = $resp->withStatus(400)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode(["user id and JWT no corresponding"]));
            return $rs;
        }
    }
}