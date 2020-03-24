define({ "api": [
  {
    "type": "get",
    "url": "http://api.commande.local:19080/commandes/{id}",
    "title": "Description d'une commande",
    "name": "getCommand",
    "group": "Commandes",
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl http://api.commande.local:19080/commandes/cdf6302b-940b-4348-b913-3cb2052bf042?token=543fc479e422715feb9562809cdd9ca54528426fae2ec0ff2382a32b937555c3",
        "type": "curl"
      }
    ],
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>Commande unique ID.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>token de la commande.</p>"
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
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "  HTTP/1.1 200 OK\n  {\n  \"type\": \"collection\",\n  \"count\": [\n           {\n               \"id\": \"cdf6302b-940b-4348-b913-3cb2052bf042\",\n               \"created_at\": \"2019-11-08 13:45:55\",\n               \"updated_at\": \"2019-11-08 13:45:55\",\n               \"livraison\": \"2019-11-08 16:56:15\",\n               \"nom\": \"Dubois\",\n               \"mail\": \"Dubois@free.fr\",\n               \"montant\": \"40.25\",\n               \"remise\": null,\n               \"token\": \"543fc479e422715feb9562809cdd9ca54528426fae2ec0ff2382a32b937555c3\",\n               \"client_id\": null,\n               \"ref_paiement\": null,\n               \"date_paiement\": null,\n               \"mode_paiement\": null,\n               \"status\": 1\n            }\n       ]\n   }\n}",
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
    "type": "post",
    "url": "http://api.commande.local:19080/commandes/",
    "title": "Créer une commande (non-authentifié).",
    "name": "insertCommand",
    "group": "Commandes",
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl -X POST http://api.commande.local:19080/commandes/",
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
            "type": "Array",
            "optional": false,
            "field": "livraison",
            "description": "<p>Date et heure de la livraison.</p>"
          },
          {
            "group": "Parameter",
            "type": "Array",
            "optional": false,
            "field": "items",
            "description": "<p>uri et quantite des items souhaite.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "    {\n     \"nom\" : \"test\",\n     \"mail\": \"test@gmail.fr\",\n     \"livraison\" : {\n             \"date\": \"7-12-2020\",\n             \"heure\": \"12:30\"\n     },\n     \"items\" : [\n         { \"uri\": \"/sandwichs/s19003\", \"q\": 1}\n         ]\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "ressource",
            "optional": false,
            "field": "commande",
            "description": "<p>retourne les informations de la commande , le montant total et un token pour y acceder.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "    HTTP/1.1 200 OK\n    {\n         \"commande\": {\n             \"nom\": \"test\",\n             \"mail\": \"test@gmail.fr\",\n             \"livraison\": \"2020-03-24 12:07:08\"\n     },\n     \"id\": \"73011897-6bf1-4231-a021-e78bcd62ddbc\",\n     \"token\": \"672259ebfe53eda2153d4302f6063b8dc3fc4b7fa1d09f89791bee1994b7bd23\",\n     \"montant\": 15.75,\n     \"items\": [\n         {\n             \"uri\": \"/sandwichs/s19005\",\n             \"q\": 3\n         }\n     ]\n}",
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
    "header": {
      "examples": [
        {
          "title": "Header-Example:",
          "content": "{\n  \"Token\": \"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpc3MiOiJodHRwOlwvXC9hcGkuYmFja29mZmljZS5sb2NhbCIsImF1ZCI6Imh0dHA6XC9cL2FwaS5iYWNrb2ZmaWNlLmxvY2FsIiwiaWF0IjoxNTg0OTY5NzE3LCJleHAiOjE1ODQ5NzMzMTcsInVpZCI6IjA0MDg5NmVlLTg4M2MtNDBlMi1iNDA1LWVkMGU3NzIyOTlhNCIsImx2bCI6MX0.nhmmDPn-iHWCDVQTNOd1vQXHUG2V9Jw6Uk5Ml3oxooUaRId2wd1Bru1O3WFoUDA9K6MEO_Xp3CGqO3COvGAujw\"\n}",
          "type": "json"
        }
      ],
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "Bearer_Token",
            "optional": false,
            "field": "Authorization",
            "description": "<p>JWT du client connecte.</p>"
          }
        ]
      }
    },
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
            "type": "Array",
            "optional": false,
            "field": "livraison",
            "description": "<p>Date et heure de la livraison.</p>"
          },
          {
            "group": "Parameter",
            "type": "Array",
            "optional": false,
            "field": "items",
            "description": "<p>uri et quantite des items souhaite.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "{\n    \"nom\" : \"test\",\n    \"mail\": \"test@gmail.fr\",\n    \"client_id\" : 105,\n    \"livraison\" : {\n        \"date\": \"7-12-2020\",\n        \"heure\": \"12:30\"\n    },\n     \"items\" : [\n        { \"uri\": \"/sandwichs/s19002\", \"q\": 2}\n        ]\n }",
          "type": "json"
        }
      ]
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "ressource",
            "optional": false,
            "field": "commande",
            "description": "<p>retourne les informations de la commande , le montant total et un token pour y acceder.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "    HTTP/1.1 200 OK\n    {\n         \"commande\": {\n             \"nom\": \"test\",\n             \"mail\": \"test@gmail.fr\",\n             \"livraison\": \"2020-03-24 12:07:08\"\n     },\n     \"id\": \"be709896-f625-45b4-9157-ad301b672cea\",\n     \"token\": \"5f21e110897750f6106a937ea29384ae4955dd4e96d5da7f6fe0ed43b0398266\",\n     \"montant\": 15.75,\n     \"items\": [\n         {\n             \"uri\": \"/sandwichs/s19005\",\n             \"q\": 3\n         }\n     ]\n}",
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
    "url": "http://api.commande.local:19080/client/{user_id}/commandes",
    "title": "Obtenir l'historique des commandes d'un client.",
    "name": "userHistoric",
    "group": "User",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "Bearer_Token",
            "optional": false,
            "field": "Authorization",
            "description": "<p>JWT du client connecte.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Header-Example:",
          "content": "{\n  \"Token\": \"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpc3MiOiJodHRwOlwvXC9hcGkuYmFja29mZmljZS5sb2NhbCIsImF1ZCI6Imh0dHA6XC9cL2FwaS5iYWNrb2ZmaWNlLmxvY2FsIiwiaWF0IjoxNTg0OTY5NzE3LCJleHAiOjE1ODQ5NzMzMTcsInVpZCI6IjA0MDg5NmVlLTg4M2MtNDBlMi1iNDA1LWVkMGU3NzIyOTlhNCIsImx2bCI6MX0.nhmmDPn-iHWCDVQTNOd1vQXHUG2V9Jw6Uk5Ml3oxooUaRId2wd1Bru1O3WFoUDA9K6MEO_Xp3CGqO3COvGAujw\"\n}",
          "type": "json"
        }
      ]
    },
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl http://api.commande.local:19080/client/101/commandes",
        "type": "curl"
      }
    ],
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "user_id",
            "description": "<p>id du client.</p>"
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
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "  HTTP/1.1 200 OK\n  {\n   \"commandes\": {\n       \"commandes\": [\n             {\n                 \"commande\": {\n                       \"id\": \"b9536d63-465d-49ee-b8eb-5daf0e8c138c\",\n                       \"token\": \"33980a6ada52bc731df0e66779bc8de8b24788a20821ab9f513800d19d213e64\",\n                       \"status\": 1,\n                       \"montant\": \"157.50\",\n                       \"created_at\": \"2020-03-24T00:18:18.000000Z\",\n                       \"livraison\": \"2020-03-24 12:18:18\",\n                       \"mail_client\": \"jojo@gmail.fr\",\n                       \"nom_client\": \"jojo\"\n                 }\n            }\n       ]\n   }\n}",
          "type": "json"
        }
      ]
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
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "Bearer_Token",
            "optional": false,
            "field": "Authorization",
            "description": "<p>JWT du client connecte.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Header-Example:",
          "content": "{\n  \"Token\": \"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpc3MiOiJodHRwOlwvXC9hcGkuYmFja29mZmljZS5sb2NhbCIsImF1ZCI6Imh0dHA6XC9cL2FwaS5iYWNrb2ZmaWNlLmxvY2FsIiwiaWF0IjoxNTg0OTY5NzE3LCJleHAiOjE1ODQ5NzMzMTcsInVpZCI6IjA0MDg5NmVlLTg4M2MtNDBlMi1iNDA1LWVkMGU3NzIyOTlhNCIsImx2bCI6MX0.nhmmDPn-iHWCDVQTNOd1vQXHUG2V9Jw6Uk5Ml3oxooUaRId2wd1Bru1O3WFoUDA9K6MEO_Xp3CGqO3COvGAujw\"\n}",
          "type": "json"
        }
      ]
    },
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl http://api.commande.local:19080/client/101",
        "type": "curl"
      }
    ],
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "user_id",
            "description": "<p>id du client.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Json",
            "optional": false,
            "field": "client",
            "description": "<p>informations concernant le client connecté.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n{\n  \"nom client\": \"pepe\",\n  \"user email\": \"pepe@gmail.com\",\n  \"user cumul achats\": \"315.00\"\n}",
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
    "url": "http://api.commande.local:19080/client/signin",
    "title": "Connexion client.",
    "name": "userSignin",
    "group": "User",
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl -X POST http://api.commande.local:19080/client/signin",
        "type": "curl"
      }
    ],
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "Basic_Auth",
            "optional": false,
            "field": "Authorization",
            "description": "<p>email and password du client.</p>"
          }
        ]
      }
    },
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
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Json",
            "optional": false,
            "field": "token",
            "description": "<p>retourne un JWT au client.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n{\n  \"token\" : \"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpc3MiOiJodHRwOlwvXC9hcGkuYmFja29mZmljZS5sb2NhbCIsImF1ZCI6Imh0dHA6XC9cL2FwaS5iYWNrb2ZmaWNlLmxvY2FsIiwiaWF0IjoxNTg1MDA2MDU1LCJleHAiOjE1ODUwMDk2NTUsInVpZCI6MTAxLCJsdmwiOjF9.yvr5HKtcUIT2NQoWlMHQxU_ZSMjQ2cPlFnRL3ZUyWxmBBNQhIwQ_eqyS_wMVsOW_g9V__MqD2_Ydu4_Syg3CIg\"\n}.",
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
        "content": "curl -X POST http://api.commande.local:19080/client/signup",
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
          "content": "{\n  \"nom_client\": \"test\",\n  \"mail_client\": \"test@gmail.com\",\n  \"passwd\": \"test\"\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Json",
            "optional": false,
            "field": "Message",
            "description": "<p>confirme que le compte a bien été crée.</p>"
          }
        ]
      },
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
