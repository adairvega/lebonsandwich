define({ "api": [
  {
    "type": "get",
    "url": "http://api.catalogue.local:19180/categories/{id}",
    "title": "Description d'une catégorie",
    "name": "getCategorie",
    "group": "Catégories",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>Categorie unique ID.</p>"
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
            "field": "categorie",
            "description": "<p>Description de la catégorie.</p>"
          },
          {
            "group": "Success 200",
            "type": "Array",
            "optional": false,
            "field": "links",
            "description": "<p>Liens vers la catégorie ou les sandwichs.</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "src/control/CategoriesController.php",
    "groupTitle": "Catégories"
  },
  {
    "type": "get",
    "url": "http://api.catalogue.local:19180/categories/{id}/{sandwichs}",
    "title": "Obtenir la liste des catégories de sandwichs",
    "name": "getCategorieSandwich",
    "group": "Catégories",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>Categorie unique ID.</p>"
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
            "field": "categorie",
            "description": "<p>Collection des catégories.</p>"
          },
          {
            "group": "Success 200",
            "type": "Array",
            "optional": false,
            "field": "sandwich",
            "description": "<p>Collection des sandwichs.</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "src/control/CategoriesController.php",
    "groupTitle": "Catégories"
  },
  {
    "type": "get",
    "url": "http://api.catalogue.local:19180/categories",
    "title": "Obtenir toutes les catégories",
    "name": "getCategories",
    "group": "Catégories",
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Array",
            "optional": false,
            "field": "categorie",
            "description": "<p>Liste des catégories.</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "src/control/CategoriesController.php",
    "groupTitle": "Catégories"
  },
  {
    "type": "get",
    "url": "http://api.catalogue.local:19180/sandwich/{uri}",
    "title": "Description d'un sandwich",
    "name": "getNameSandwich",
    "group": "Sandwichs",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "uri",
            "description": "<p>Lien vers le sandwich.</p>"
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
            "field": "sandwich",
            "description": "<p>Description d'un sandwich.</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "src/control/CategoriesController.php",
    "groupTitle": "Sandwichs"
  },
  {
    "type": "get",
    "url": "http://api.catalogue.local:19180/sandwichs",
    "title": "Obtenir tous les sandwichs",
    "name": "getSandwichs",
    "group": "Sandwichs",
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Array",
            "optional": false,
            "field": "sandwichs",
            "description": "<p>Liste des sandwichs.</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "src/control/CategoriesController.php",
    "groupTitle": "Sandwichs"
  }
] });