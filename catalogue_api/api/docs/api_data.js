define({ "api": [
  {
    "type": "get",
    "url": "http://api.catalogue.local:19180/categories/{id}",
    "title": "Obtenir la description d'une catégorie.",
    "name": "getCategorie",
    "group": "Catégories",
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl http://api.catalogue.local:19180/categories/1",
        "type": "curl"
      }
    ],
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Numero",
            "optional": false,
            "field": "id",
            "description": "<p>numero de la categorie.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "resource",
            "optional": false,
            "field": "categorie",
            "description": "<p>Description d'une categorie.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "    HTTP/1.1 200 OK\n{\n\n  \"type\": \"resource\",\n  \"date\": \"2020-03-23\",\n  \"categorie\": {\n      \"id\": 1,\n      \"nom\": \"traditionnel\",\n      \"description\": \"nos sandwiches et boissons classiques\"\n},\n\"links\": {\n    \"sandwichs\": {\n         \"href\": \"http://api.catalogue.local:19180/categories/1/sandwichs/\"\n     },\n     \"self\": {\n     \"href\": \"http://api.catalogue.local:19180/categories/1/\"\n         }\n     }\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "src/control/CategoriesController.php",
    "groupTitle": "Catégories"
  },
  {
    "type": "get",
    "url": "http://api.catalogue.local:19180/categories/{id}/sandwichs",
    "title": "Obtenir la liste des sandwichs d'une catégorie.",
    "name": "getCategorieSandwich",
    "group": "Catégories",
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl http://api.catalogue.local:19180/categories/1/sandwichs",
        "type": "curl"
      }
    ],
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Numero",
            "optional": false,
            "field": "id",
            "description": "<p>numero de la categorie.</p>"
          }
        ]
      }
    },
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
          "content": "    HTTP/1.1 200 OK\n{\n  \"type\": \"collection\",\n  \"count\": 1,\n  \"size\": 1,\n  \"categorie\": \"veggie\",\n  \"sandwichs\": [\n      {\n         \"sandwich\": {\n             \"ref\": \"s19007\",\n             \"nom\": \"le club sandwich\",\n             \"description\": \"le club sandwich comme à Saratoga : pain toasté, filet de dinde, bacon, laitue, tomate\",\n             \"type_pain\": \"mie\"\n             }\n         }\n     ]\n }",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "src/control/CategoriesController.php",
    "groupTitle": "Catégories"
  },
  {
    "type": "get",
    "url": "http://api.catalogue.local:19180/categories",
    "title": "Obtenir la liste des catégories de sandwich.",
    "name": "getCategories",
    "group": "Catégories",
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl http://api.catalogue.local:19180/categories",
        "type": "curl"
      }
    ],
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "JsonArray",
            "optional": false,
            "field": "categories",
            "description": "<p>Tableau de toutes les categories.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "    HTTP/1.1 200 OK\n[\n {\n     \"categories\": {\n         \"id\": 4,\n         \"nom\": \"world\",\n         \"description\": \"nos produits du monde, à découvrir !\"\n         }\n },\n {\n     \"categories\": {\n     \"id\": 2,\n     \"nom\": \"chaud\",\n     \"description\": \"nos sandwiches et boissons chaudes\"\n         }\n }\n]",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "src/control/CategoriesController.php",
    "groupTitle": "Catégories"
  },
  {
    "type": "get",
    "url": "http://api.catalogue.local:19180/sandwichs/{uri}",
    "title": "Obtenir la description d'un sandwich.",
    "name": "getNameSandwich",
    "group": "Sandwichs",
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl http://api.catalogue.local:19180/sandwichs/s19002",
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
            "field": "uri",
            "description": "<p>numero de reference du sandwich.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "JsonArray",
            "optional": false,
            "field": "sandwich",
            "description": "<p>Description d'un sandwich.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n {\n     \"ref\": \"/sandwichs/s19002\",\n     \"nom\": \"le jambon-beurre\",\n     \"prix\": \"4.50\"\n }",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "src/control/CategoriesController.php",
    "groupTitle": "Sandwichs"
  },
  {
    "type": "get",
    "url": "http://api.catalogue.local:19180/sandwichs",
    "title": "Obtenir la liste des sandwichs du catalogue.",
    "name": "getSandwichs",
    "group": "Sandwichs",
    "examples": [
      {
        "title": "Example usage:",
        "content": "curl http://api.catalogue.local:19180/sandwichs",
        "type": "curl"
      }
    ],
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "JsonArray",
            "optional": false,
            "field": "sandwichs",
            "description": "<p>tableau de tous les sandwichs.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n [\n     {\n         \"sandwichs\": {\n             \"ref\": \"s19001\",\n             \"nom\": \"le bucheron\",\n             \"description\": \"un sandwich de bucheron : frites, fromage, saucisse, steack, lard grillés, mayo\",\n             \"type_pain\": \"baguette\"\n         }\n     },\n     {\n         \"sandwichs\": {\n             \"ref\": \"s19002\",\n             \"nom\": \"le jambon-beurre\",\n             \"description\": \"le jambon-beurre traditionnel, avec des cornichons\",\n             \"type_pain\": \"baguette\"\n         }\n     }\n ]",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "src/control/CategoriesController.php",
    "groupTitle": "Sandwichs"
  }
] });
