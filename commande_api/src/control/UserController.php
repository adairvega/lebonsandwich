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
     * @apiParam    user_id    uuid client.
     * 
     * @apiSuccess {String} nom_client Nom du client.
     * @apiSuccess {Mail} mail_client  Mail du client.
     * @apiSuccess {Number} cumul_achat Cumul des achats du client.
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
     *     curl -X POST http://api.commande.local:19080/user/signup
     * @apiParam {String} nom_client Le prenom du client.
     * @apiParam {String} mail_client l'adresse mail du client.
     * @apiParam {String} passwd Le mot de passe.
     * @apiParamExample {json} Request-Example:
     *     {
     * "nom_client": "test",
     * "mail_client": "test@gmail.com",
     * "passwd": "test"
     * }
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "votre compte utilisateur a bien été crée."
     *     }
     */
    public function userSignup(Request $req, Response $resp, array $args)
    {
        if (!$req->getAttribute('errors')) {
            $user = new user();
            $getParsedBody = $req->getParsedBody();
            $user->nom_client = filter_var($getParsedBody["nom_client"], FILTER_SANITIZE_STRING);
            $user->mail_client = filter_var($getParsedBody["mail_client"], FILTER_SANITIZE_EMAIL);
            $user->passwd = password_hash($getParsedBody["passwd"], PASSWORD_DEFAULT);
            $user->created_at = date("Y-m-d H:i:s");
            $user->updated_at = date("Y-m-d H:i:s");
            $user->save();
            $rs = $resp->withStatus(200)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode("votre compte utilisateur a bien été crée"));
            return $rs;
        } else {
            $errors = $req->getAttribute('errors');
            $errorsArray = array();
            foreach ($errors as $error) {
                $errorsArray["error"][] = $error[0];
            }
            $rs = $resp->withStatus(400)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode($errorsArray["error"]));
            return $rs;
        }
    }

    /**
     * @api {post} http://api.commande.local:19080/client/signin Connexion client.
     * @apiName userSignin
     * @apiGroup User
     * @apiExample {curl} Example usage:
     *     curl -X POST http://api.commande.local:19080/user/signin
     * @apiParam {String} user_mail l'adresse mail du client.
     * @apiParam {String} user_passwd Le mot de passe.
     * @apiParamExample {json} Request-Example:
     *     {
     *      "user_mail": "test@gmail.com",
     *       "user_passwd": "test"
     *      }
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *       "token" : "0066a5ddfbade9a009f8d9c09333acd6f146690d88518b494840051494229c8e"
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
     * @api {get} http://api.commande.local:19080/client/{user_id}/{commandes} Obtenir l'historique des commandes d'un client.
     * @apiName userHistoric
     * @apiGroup User
     * 
     * @apiParam user_id    uuid client.
     * 
     * @apiSuccess {Array} commandes Liste de toutes les commandes du client.
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