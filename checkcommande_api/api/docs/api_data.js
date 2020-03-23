define({ "api": [
  {
    "type": "get",
    "url": "http://api.checkcommande.local:19280/commandes/{id}",
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
            "field": "command",
            "description": "<p>Description de la commande.</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "src/control/PointVenteController.php",
    "groupTitle": "Commandes"
  },
  {
    "type": "get",
    "url": "http://api.checkcommande.local:19280/commandes/",
    "title": "Obtenir toutes les commandes.",
    "name": "getCommands",
    "group": "Commandes",
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Array",
            "optional": false,
            "field": "size",
            "description": "<p>Pagination.</p>"
          },
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
            "field": "command",
            "description": "<p>Description de la commande.</p>"
          }
        ]
      }
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
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "Id",
            "description": "<p>id de la commande.</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n{\n  \"success\"\n}",
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
    "url": "http://api.checkcommande.local:19280/commandes/{id}/{items}",
    "title": "Obtenir les items d'une commande.",
    "name": "getItems",
    "group": "Items",
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
            "type": "String",
            "optional": false,
            "field": "id",
            "description": "<p>ID de la commande.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "items",
            "description": "<p>Items de la commande.</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "src/control/PointVenteController.php",
    "groupTitle": "Items"
  }
] });
