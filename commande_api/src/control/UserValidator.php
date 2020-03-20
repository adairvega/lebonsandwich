<?php

namespace lbs\command\control;

use \Respect\Validation\Validator as v;

class UserValidator
{
    public static function validators()
    {
        return
            [
                'nom_client' => v::StringType()->alpha(),
                'mail_client' => v::email(),
            ];
    }
}
