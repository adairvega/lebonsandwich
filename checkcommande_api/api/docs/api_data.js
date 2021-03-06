define({ "api": [
  {
    "type": "get",
    "url": "http://api.checkcommande.local:19280/commandes/{id}",
    "title": "Obtenir la description détaillée d'une commande.",
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
            "description": "<p>id unique de la commande.</p>"
          }
        ]
      }
    },
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl http://api.checkcommande.local:19280/commandes/cdf6302b-940b-4348-b913-3cb2052bf042",
        "type": "curl"
      }
    ],
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "ressource",
            "optional": false,
            "field": "Collection",
            "description": "<p>description détaillée d'une commande.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "    HTTP/1.1 200 OK\n\n{\n  \"type\": \"resource\",\n  \"links\": {\n     \"self\": \"http://api.checkcommande.local:19280/commandes/cdf6302b-940b-4348-b913-3cb2052bf042\",\n     \"items\": \"http://api.checkcommande.local:19280/commandes/cdf6302b-940b-4348-b913-3cb2052bf042/items\"\n},\n \"command\": {\n     \"id\": \"cdf6302b-940b-4348-b913-3cb2052bf042\",\n     \"created_at\": \"2019-11-08T13:45:55.000000Z\",\n     \"livraison\": \"2019-11-08 16:56:15\",\n     \"nom\": \"Dubois\",\n     \"mail\": \"Dubois@free.fr\",\n     \"montant\": \"40.25\",\n     \"items\": [\n         {\n             \"uri\": \"/sandwichs/s19005\",\n             \"libelle\": \"la mer\",\n             \"tarif\": \"5.25\",\n             \"quantite\": 2\n         }\n     ]\n   }\n }",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "src/control/PointVenteController.php",
    "groupTitle": "Commandes"
  },
  {
    "type": "get",
    "url": "http://api.checkcommande.local:19280/commandes/",
    "title": "Obtenir la listes de toutes les commandes.",
    "name": "getCommands",
    "group": "Commandes",
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl http://api.checkcommande.local:19280/commandes/",
        "type": "curl"
      }
    ],
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Collection",
            "optional": false,
            "field": "Collection",
            "description": "<p>Description et sandwichs d'une categorie.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "    HTTP/1.1 200 OK\n{\n  \"type\": \"collection\",\n  \"count\": 1515,\n  \"size\": 10,\n  \"links\": 0,\n  \"categorie\": \"veggie\",\n   commandes\": [\n     {\n       \"commande\": {\n          \"id\": \"06d23c7f-3a7d-4499-b7f1-0bb53ae40495\",\n          \"nom\": \"Collet\",\n          \"created_at\": \"2019-11-08T13:45:56.000000Z\",\n          \"livraison\": \"2019-11-09 13:06:06\",\n          \"status\": 1\n      },\n      \"links\": {\n          \"self\": {\n                 \"href\": \"http://api.checkcommande.local:19280/commandes/06d23c7f-3a7d-4499-b7f1-0bb53ae40495\"\n          }\n       }\n    }\n  ]\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "src/control/PointVenteController.php",
    "groupTitle": "Commandes"
  },
  {
    "type": "put",
    "url": "http://api.checkcommande.local:19280/commandes/{id}",
    "title": "Modifier l'état d'une commande.",
    "name": "updateCommand",
    "group": "Commandes",
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl -X PUT http://api.checkcommande.local:19280/commandes/d9b0aef8-a42d-439d-a7d3-fa5672d1e1f1/",
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
            "field": "Id",
            "description": "<p>id de la commande a change.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "{\n   \"status\" : 2\n }",
          "type": "json"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": " HTTP/1.1 200 OK\n {\n   \"id\": \"d9b0aef8-a42d-439d-a7d3-fa5672d1e1f1\",\n   \"created_at\": \"2020-03-23 22:08:41\",\n   \"updated_at\": \"2020-03-23 22:54:21\",\n   \"livraison\": \"2020-03-23 10:08:41\",\n   \"nom\": \"jojo\",\n   \"mail\": \"jojo@gmail.fr\",\n   \"montant\": \"157.50\",\n   \"remise\": null,\n   \"token\": \"fbaf3a6d7385b3019b82f024b1882ead1dd15fd0a34ad404ce0aa07cb9cd44a8\",\n   \"client_id\": null,\n   \"ref_paiement\": null,\n   \"date_paiement\": null,\n   \"mode_paiement\": null,\n   \"status\": 2\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "src/control/PointVenteController.php",
    "groupTitle": "Commandes"
  },
  {
    "type": "get",
    "url": "http://api.checkcommande.local:19280/commandes/{id}/items",
    "title": "Obtenir les items d'une commande.",
    "name": "getItems",
    "group": "Items",
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl http://api.checkcommande.local:19280/commandes/d9b0aef8-a42d-439d-a7d3-fa5672d1e1f1/items",
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
            "description": "<p>id unique de la commande.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "item",
            "optional": false,
            "field": "items",
            "description": "<p>les items d'une commande.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "    HTTP/1.1 200 OK\n\n{\n  \"type\": \"item\",\n  \"id\": \"d9b0aef8-a42d-439d-a7d3-fa5672d1e1f1\"\n  \"items\": [\n      {\n         \"id\": 6796,\n         \"uri\": \"/sandwichs/s19005\",\n         \"libelle\": \"la mer\",\n         \"tarif\": \"5.25\",\n         \"quantite\": 30,\n         \"commande_id\": \"d9b0aef8-a42d-439d-a7d3-fa5672d1e1f1\"\n         }\n      ]\n   }",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "src/control/PointVenteController.php",
    "groupTitle": "Items"
  }
] });
