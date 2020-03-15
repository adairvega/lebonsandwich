<?php


namespace lbs\command\control;

use Firebase\JWT\JWT;
use \lbs\common\bootstrap\Eloquent;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;

class Middleware
{
    protected $c;

    public function __construct(\Slim\Container $c = null)
    {
        $this->c = $c;
    }

    public function checkAuthorization(Request $rq, Response $rs, callable $next)
    {
        if (!empty($getHeader = $rq->getHeader("Authorization")[0]) and strpos($getHeader, "Basic") !== false) {
            $rq = $rq->withAttribute("getHeader", $getHeader);
            return $next($rq, $rs);
        } else {
            $rs = $rs->withStatus(401)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode(['type' => 'error', 'Error_code' => 401, 'message :' => 'no data in Basic Authorization']));
            return $rs;
        }
    }

    public function decodeAuthorization(Request $rq, Response $rs, callable $next)
    {
        $getHeader = $rq->getAttribute("getHeader");
        $getHeader_value = substr($getHeader, 6);
        $getHeader_value_decode = base64_decode($getHeader_value);
        $dote_position = strpos($getHeader_value_decode, ':');
        $user_name = substr($getHeader_value_decode, 0, $dote_position);
        $user_passwd = substr($getHeader_value_decode, $dote_position + 1);
        $rq = $rq->withAttribute("user_name", $user_name);
        $rq = $rq->withAttribute("user_passwd", $user_passwd);
        return $next($rq, $rs);
    }


    function checkToken(Request $rq, Response $rs, callable $next)
    {
        if (!empty($rq->getQueryParams('token', null))) {
            $token = $rq->getQueryParams("token");
            $rq = $rq->withAttribute("token", $token);
            return $next($rq, $rs);
        } else {
            $rs = $rs->withStatus(401)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode(['type' => 'error', 'Error_code' => 401, 'message :' => 'no token found in URL']));
            return $rs;
        }
    }

    function getToken(Request $rq, Response $rs, callable $next)
    {
        $token = $rq->getAttribute("token");
        $token = $token["token"];
        $rq = $rq->withAttribute("token", $token);
        return $next($rq, $rs);
    }

    public function checkJWT(Request $rq, Response $rs, callable $next)
    {
        if (!empty($h = $rq->getHeader("Authorization")[0]) and strpos($h, "Bearer") !== false) {
            return $next($rq, $rs);
        } else {
            $rs = $rs->withStatus(401)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode(['type' => 'error', 'Error_code' => 401, 'message :' => 'no JWT found in Authorization']));
            return $rs;
        }
    }

    public function checkHeaderOrigin(Request $rq, Response $rs, callable $next)
    {
        if ($rq->getHeader('Origin')) {
            return $next($rq, $rs);
        } else {
            $rs = $rs->withStatus(401)
                ->withHeader('Content-Type', 'application/json;charset=utf-8');
            $rs->getBody()->write(json_encode(['type' => 'error', 'Error_code' => 401, 'message :' => 'no Origin Header found']));
            return $rs;
        }
    }

    public function headersCORS(Request $rq, Response $rs, callable $next)
    {
        $response = $next($rq, $rs)
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', '*')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
        return $response;
    }


    function decodeJWT(Request $rq, Response $rs, callable $next)
    {
        try {
            $h = $rq->getHeader('Authorization')[0];
            $tokenstring = sscanf($h, "Bearer %s")[0];
            $token = JWT::decode($tokenstring, "secret", ['HS512']);
            $rq = $rq->withAttribute("token", $token);
            return $next($rq, $rs);
        } catch (ExpiredException $e) {
            return $rs;
        } catch (SignatureInvalidException $e) {
            return $rs;
        } catch (BeforeValidException $e) {
            return $rs;
        } catch (\UnexpectedValueException $e) {
            return $rs;
        }
    }
}