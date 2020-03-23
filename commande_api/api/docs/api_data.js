define({ "api": [
  {
    "type": "get",
    "url": "http://api.commande.local:19080/commandes/{id}",
    "title": "Description d'une commande",
    "name": "getCommand",
    "group": "Commandes",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>Commande unique ID.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Array",
            "optional": false,
            "field": "links",
            "description": "<p>Liens vers la commande ou les items de la commande.</p>"
          },
          {
            "group": "Success 200",
            "type": "Array",
            "optional": false,
            "field": "commande",
            "description": "<p>Description de la commande.</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "src/control/CommandesController.php",
    "groupTitle": "Commandes"
  },
  {
    "type": "get",
    "url": "http://api.commande.local:19080/commandes/{id}/{items}",
    "title": "Liste des items d'une commande",
    "name": "getCommandItems",
    "group": "Commandes",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>Commande unique ID.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Array",
            "optional": false,
            "field": "items",
            "description": "<p>Liste des items d'une commande.</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "src/control/CommandesController.php",
    "groupTitle": "Commandes"
  },
  {
    "type": "get",
    "url": "http://api.commande.local:19080/commandes",
    "title": "Liste des commandes",
    "name": "getCommands",
    "group": "Commandes",
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Array",
            "optional": false,
            "field": "command",
            "description": "<p>Liste de toutes les commandes.</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "src/control/CommandesController.php",
    "groupTitle": "Commandes"
  },
  {
    "type": "post",
    "url": "http://api.commande.local:19080/commandes/",
    "title": "Créer une commande (non-authentifié).",
    "name": "insertCommand",
    "group": "Commandes",
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl -X POST http://api.commande.local:19080/commandes/auth",
        "type": "curl"
      }
    ],
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "nom",
            "description": "<p>le nom du client.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "mail",
            "description": "<p>l'adresse mail du client.</p>"
          },
          {
            "group": "Parameter",
            "type": "date",
            "optional": false,
            "field": "date",
            "description": "<p>Date de livraison.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>Token d'authentification.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "item",
            "description": "<p>Quantite des items.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "uri",
            "description": "<p>Lien vers l'item.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "    {\n\"nom\": \"Dubois\"\n\"email\": \"Dubois@free.fr\",\n\"commande\" :\n{\n\"livraison\" : \"2020/02/25/ 02:55:02\"\n \"items\" :\n{\n\"uri\" : \"/sandwichs/s19005\"\n\"quantite\" : \"2\"\n}\n }\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n{\n  \"token\" : \"543fc479e422715feb9562809cdd9ca54528426fae2ec0ff2382a32b937555c3\"\n}.",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "src/control/CommandesController.php",
    "groupTitle": "Commandes"
  },
  {
    "type": "post",
    "url": "http://api.commande.local:19080/commandes/auth",
    "title": "Créer une commande (authentifié).",
    "name": "insertCommandAuth",
    "group": "Commandes",
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl -X POST http://api.commande.local:19080/commandes/auth",
        "type": "curl"
      }
    ],
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "nom",
            "description": "<p>le nom du client.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "mail",
            "description": "<p>l'adresse mail du client.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "client_id",
            "description": "<p>numero de l'id du client connecte.</p>"
          },
          {
            "group": "Parameter",
            "type": "date",
            "optional": false,
            "field": "date",
            "description": "<p>Date de livraison.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>Token d'authentification.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "item",
            "description": "<p>Quantite des items.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "uri",
            "description": "<p>Lien vers l'item.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "{\n\"nom\" : \"jojo\",\n\"mail\": \"jojo@gmail.fr\",\n\"client_id\" : 105,\n\"livraison\" : {\n\"date\": \"7-12-2020\",\n\"heure\": \"12:30\"\n},\n\"items\" : [\n{ \"uri\": \"/sandwichs/s19002\", \"q\": 2}\n]\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n{\n  \"token\" : \"543fc479e422715feb9562809cdd9ca54528426fae2ec0ff2382a32b937555c3\"\n}.",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "src/control/CommandesController.php",
    "groupTitle": "Commandes"
  },
  {
    "type": "get",
    "url": "http://api.commande.local:19080/client/{user_id}/{commandes}",
    "title": "Obtenir l'historique des commandes d'un client.",
    "name": "userHistoric",
    "group": "User",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "optional": false,
            "field": "user_id",
            "description": "<p>uuid client.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Array",
            "optional": false,
            "field": "commandes",
            "description": "<p>Liste de toutes les commandes du client.</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "src/control/UserController.php",
    "groupTitle": "User"
  },
  {
    "type": "get",
    "url": "http://api.commande.local:19080/client/{user_id}",
    "title": "Obtenir les données du client.",
    "name": "userProfiles",
    "group": "User",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "optional": false,
            "field": "user_id",
            "description": "<p>uuid client.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "nom_client",
            "description": "<p>Nom du client.</p>"
          },
          {
            "group": "Success 200",
            "type": "Mail",
            "optional": false,
            "field": "mail_client",
            "description": "<p>Mail du client.</p>"
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "cumul_achat",
            "description": "<p>Cumul des achats du client.</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "src/control/UserController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "http://api.commande.local:19080/client/signin",
    "title": "Connexion client.",
    "name": "userSignin",
    "group": "User",
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl -X POST http://api.commande.local:19080/user/signin",
        "type": "curl"
      }
    ],
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "user_mail",
            "description": "<p>l'adresse mail du client.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "user_passwd",
            "description": "<p>Le mot de passe.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "{\n \"user_mail\": \"test@gmail.com\",\n  \"user_passwd\": \"test\"\n }",
          "type": "json"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n{\n  \"token\" : \"0066a5ddfbade9a009f8d9c09333acd6f146690d88518b494840051494229c8e\"\n}.",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "src/control/UserController.php",
    "groupTitle": "User"
  },
  {
    "type": "post",
    "url": "http://api.commande.local:19080/client/signup",
    "title": "Creation d'un client.",
    "name": "userSignup",
    "group": "User",
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl -X POST http://api.commande.local:19080/user/signup",
        "type": "curl"
      }
    ],
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "nom_client",
            "description": "<p>Le prenom du client.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "mail_client",
            "description": "<p>l'adresse mail du client.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "passwd",
            "description": "<p>Le mot de passe.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "    {\n\"nom_client\": \"test\",\n\"mail_client\": \"test@gmail.com\",\n\"passwd\": \"test\"\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n{\n  \"votre compte utilisateur a bien été crée.\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "src/control/UserController.php",
    "groupTitle": "User"
  }
] });
