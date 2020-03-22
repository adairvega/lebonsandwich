<?php

namespace lbs\command\control;

use \Respect\Validation\Validator as v;

class CommandValidator
{
    public static function validators()
    {
        return
            [
                'nom' => v::StringType()->alpha()->notEmpty(),
                'mail' => v::email()->notEmpty(),
                'livraison' => [
                    'date' => v::date('d-m-Y')->min('now')->notEmpty(),
                    'heure' => v::date('H:i')->notEmpty(),
                ],
                "client_id" => v::optional(v::intVal()),
                "items" => v::arrayVal()->each(v::arrayVal()
                    ->key('uri', v::stringType()->notEmpty())
                    ->key('q', v::intVal()->notEmpty()))
            ];
    }
}
