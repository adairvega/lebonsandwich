<?php

return [
	'settings' => [
		'displayErrorDetails' => true,
		'dbconf' => __DIR__.'/commande.db.conf.ini'
	],

	'notFoundHandler' => function($c) {
		return function ($req, $resp) {
			return \lbs\command\api\errors\BadUri::error($req, $resp);
		};
	},
	
	'notAllowedHandler' => function($c) {
		return function ($req, $resp, $methods) {
			return \lbs\command\api\errors\NotAllowed::error($req, $resp, $methods);
		};
	},

	'phpErrorHandler' => function($c) {
		return function ($req, $resp, $error) {
			return \lbs\command\api\errors\Internal::error($req, $resp, $error);
		};
	}
	
]