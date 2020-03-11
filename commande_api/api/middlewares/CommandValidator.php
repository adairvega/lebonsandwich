<?php

namespace lbs\command\api\middlewares;

use \Respect\Validation\Validator as v;

class CommandValidator
{
    public static function validators()
    {
        return
            [
                'nom' => v::StringType()->alpha(),
                'mail' => v::email(),
                'livraison' => [
                    'date' => v::date('d-m-Y')->min('now'),
                    'heure' => v::date('H:i'),
                ],
                "client_id" => v::optional(v::intVal()),
                "items" => v::arrayVal()->each(v::arrayVal()
                    ->key('uri', v::stringType())
                    ->key('q', v::intVal()))
            ];
    }
}
