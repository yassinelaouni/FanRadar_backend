## 🔐 API d'Authentification

### Contrôleur : `Api\AuthentificationController`

---

### ✅ Enregistrement

**URL :** `POST /api/register`

**Champs requis :**

- `first_name` (string, requis)  
- `last_name` (string, requis)  
- `email` (string, requis, unique)  
- `password` (string, requis, min:6)  
- `profile_image` (file, optionnel – jpg/jpeg/png)

**Réponse :**
```json
{
  "message": "Inscription réussie.",
  "user": {
    "id": 1,
    "first_name": "Jean",
    "last_name": "Dupont",
    "email": "jean@example.com",
    "profile_image": "default.png",
    "role": ["user"],
    "permissions": []
  },
  "token": "TOKEN_SANCTUM"
}
```

---

### 🔓 Connexion

**URL :** `POST /api/login`

**Champs requis :**

- `email` (string)  
- `password` (string)

**Réponse (succès) :**
```json
{
  "message": "Connexion réussie.",
  "user": {
    "id": 1,
    "first_name": "Jean",
    "last_name": "Dupont",
    "email": "jean@example.com",
    "profile_image": "default.png",
    "role": ["user"],
    "permissions": []
  },
  "token": "TOKEN_SANCTUM"
}
```

**Réponse (échec) :**
```json
{
  "message": "Email ou mot de passe invalide."
}
```

---

### 🚪 Déconnexion

**URL :** `POST /api/logout`

**Headers :**
```
Authorization: Bearer {token}
```

**Réponse :**
```json
{
  "message": "Logout successful"
}
```

---

### 🔄 Déconnexion de tous les appareils

**URL :** `POST /api/logoutfromAllDevices`

**Headers :**
```
Authorization: Bearer {token}
```

**Réponse :**
```json
{
  "message": "Logged out from all devices."
}
```

## 🛒 API Produits

### Contrôleur : `ProductController`

---

### 📄 Liste des produits (paginée)

**URL :** `GET /api/products`

**Description :**  
Retourne une liste paginée (10 par page) des produits avec leurs médias associés.

**Réponse (succès) :**
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "product_name": "Produit A",
      "description": "Description du produit",
      "price": 99.99,
      "stock": 10,
      "promotion": 20,
      "sale_start_date": "2025-08-01",
      "sale_end_date": "2025-08-15",
      "medias": [
        {
          "id": 1,
          "file_path": "products/images/image1.jpg",
          "media_type": "image"
        }
      ]
    }
  ],
  ...
}
```

---

### ➕ Création d’un produit avec médias

**URL :** `POST /api/products`

**Champs requis :**

- `product_name` (string, requis, max:255)  
- `description` (string, optionnel)  
- `price` (numeric, requis, min:0)  
- `stock` (integer, requis, min:0)  
- `promotion` (integer, optionnel, entre 0 et 100)  
- `user_id` (integer, requis, doit exister dans la table users)  
- `sale_start_date` (date, optionnel)  
- `sale_end_date` (date, optionnel, doit être après ou égal à sale_start_date)  
- `medias` (array de fichiers, optionnel) — images ou vidéos

**Réponse (succès) :**
```json
{
  "message": "Produit créé avec succès.",
  "product": {
    "id": 1,
    "product_name": "Produit A",
    "description": "Description du produit",
    "price": 99.99,
    "stock": 10,
    "promotion": 20,
    "sale_start_date": "2025-08-01",
    "sale_end_date": "2025-08-15",
    "medias": [
      {
        "id": 1,
        "file_path": "products/images/image1.jpg",
        "media_type": "image"
      }
    ]
  }
}
```

---

### 🔍 Afficher un produit

**URL :** `GET /api/products/{product}`

**Réponse (succès) :**
```json
{
  "id": 1,
  "product_name": "Produit A",
  "description": "Description du produit",
  "price": 99.99,
  "stock": 10,
  "promotion": 20,
  "sale_start_date": "2025-08-01",
  "sale_end_date": "2025-08-15",
  "medias": [
    {
      "id": 1,
      "file_path": "products/images/image1.jpg",
      "media_type": "image"
    }
  ]
}
```

---

### ✏️ Modifier un produit (sans médias)

**URL :** `PUT /api/products/{product}`

**Champs requis :**

- `product_name` (string, requis, max:255)  
- `description` (string, optionnel)  
- `price` (numeric, requis, min:0)  
- `stock` (integer, requis, min:0)  
- `promotion` (integer, optionnel, entre 0 et 100)  
- `sale_start_date` (date, optionnel)  
- `sale_end_date` (date, optionnel, doit être après ou égal à sale_start_date)  

**Réponse (succès) :**
```json
{
  "message": "Produit mis à jour.",
  "product": {
    "id": 1,
    "product_name": "Produit A",
    "description": "Description mise à jour",
    "price": 89.99,
    "stock": 8,
    "promotion": 15,
    "sale_start_date": "2025-08-01",
    "sale_end_date": "2025-08-15",
    "medias": [ /* médias existants */ ]
  }
}
```

---

### 🗑️ Supprimer un produit avec ses médias

**URL :** `DELETE /api/products/{product}`

**Réponse (succès) :**
```json
{
  "message": "Produit et ses médias supprimés."
}
```

## 📝 API Posts

### Contrôleur : `PostController`

---

### 📄 Liste des posts (paginée)

**URL :** `GET /api/posts`

**Description :**  
Retourne une liste paginée (10 par page) des posts avec l'utilisateur et leurs médias associés.

**Réponse (succès) :**
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "title": "Titre du post",
      "body": "Contenu du post",
      "user": {
        "id": 1,
        "name": "Jean Dupont",
        "email": "jean@example.com"
      },
      "medias": [
        {
          "id": 1,
          "file_path": "posts/images/image1.jpg",
          "media_type": "image"
        }
      ],
      ...
    }
  ],
  ...
}
```

---

### ➕ Création d’un post avec médias

**URL :** `POST /api/posts`

**Champs requis :**

- `title` (string, requis, max:255)  
- `body` (string, optionnel)  
- `user_id` (integer, requis, doit exister dans la table users)  
- `feedback` (integer, optionnel)  
- `schedule_at` (date, optionnel)  
- `description` (string, optionnel)  
- `content_status` (string, requis, valeurs autorisées : `draft`, `published`, `archived`)  
- `medias` (array de fichiers, optionnel) — images JPG/JPEG/PNG ou vidéos MP4/MOV, max 20Mo par fichier

**Réponse (succès) :**
```json
{
  "message": "Post créé avec succès.",
  "post": {
    "id": 1,
    "title": "Titre du post",
    "body": "Contenu du post",
    "user": {
      "id": 1,
      "name": "Jean Dupont",
      "email": "jean@example.com"
    },
    "medias": [
      {
        "id": 1,
        "file_path": "posts/images/image1.jpg",
        "media_type": "image"
      }
    ]
  }
}
```

---

### 🔍 Afficher un post spécifique

**URL :** `GET /api/posts/{post}`

**Réponse (succès) :**
```json
{
  "id": 1,
  "title": "Titre du post",
  "body": "Contenu du post",
  "user": {
    "id": 1,
    "name": "Jean Dupont",
    "email": "jean@example.com"
  },
  "medias": [
    {
      "id": 1,
      "file_path": "posts/images/image1.jpg",
      "media_type": "image"
    }
  ]
}
```

---

### ✏️ Modifier un post (sans modifier les médias)

**URL :** `PUT /api/posts/{post}`

**Champs requis :**

- `title` (string, requis, max:255)  
- `body` (string, optionnel)  
- `feedback` (integer, optionnel)  
- `schedule_at` (date, optionnel)  
- `description` (string, optionnel)  
- `content_status` (string, requis, valeurs : `draft`, `published`, `archived`)

**Réponse (succès) :**
```json
{
  "message": "Post mis à jour.",
  "post": {
    "id": 1,
    "title": "Titre mis à jour",
    "body": "Contenu mis à jour",
    "user": {
      "id": 1,
      "name": "Jean Dupont",
      "email": "jean@example.com"
    },
    "medias": [ /* médias existants */ ]
  }
}
```

---

### 🗑️ Supprimer un post avec ses médias

**URL :** `DELETE /api/posts/{post}`

**Description :**  
Supprime le post, ses médias (fichiers et enregistrements).

**Réponse (succès) :**
```json
{
  "message": "Post et ses médias supprimés."
}
```

## 🛒 API Commandes

### Contrôleur : `OrderController`

---

### 📄 Liste des commandes

**URL :** `GET /api/orders`

**Description :**  
Retourne la liste de toutes les commandes avec l’utilisateur et les produits associés.

**Réponse (succès) :**
```json
[
  {
    "id": 1,
    "user": {
      "id": 1,
      "name": "Jean Dupont",
      "email": "jean@example.com"
    },
    "products": [
      {
        "id": 10,
        "product_name": "Produit A",
        "pivot": {
          "quantity": 2
        }
      }
    ],
    "total_amount": 100.5,
    "status": "pending",
    "order_date": "2025-08-01"
  }
]
```

---

### ➕ Créer une commande avec produits

**URL :** `POST /api/orders`

**Champs requis :**

- `user_id` (integer, requis, doit exister dans la table users)  
- `total_amount` (float, requis, ≥ 0)  
- `status` (string, optionnel, valeurs possibles selon `Order::STATUSES`, par défaut `"pending"`)  
- `order_date` (date, requis)  
- `products` (array, requis) — liste des produits commandés, chaque élément doit contenir :
  - `product_id` (integer, requis, doit exister dans la table products)
  - `quantity` (integer, requis, minimum 1)

**Validation spécifique :**

- Vérification que chaque produit existe et que le stock est suffisant avant création.  
- Si stock insuffisant, renvoie une erreur 422 avec message détaillé.

**Réponse (succès) :**
```json
{
  "id": 1,
  "user_id": 1,
  "total_amount": 100.5,
  "status": "pending",
  "order_date": "2025-08-01",
  "products": [
    {
      "id": 10,
      "product_name": "Produit A",
      "pivot": {
        "quantity": 2
      }
    }
  ]
}
```

---

### 🔍 Afficher une commande spécifique

**URL :** `GET /api/orders/{order}`

**Réponse (succès) :**
```json
{
  "id": 1,
  "user": {
    "id": 1,
    "name": "Jean Dupont",
    "email": "jean@example.com"
  },
  "products": [
    {
      "id": 10,
      "product_name": "Produit A",
      "pivot": {
        "quantity": 2
      }
    }
  ],
  "total_amount": 100.5,
  "status": "pending",
  "order_date": "2025-08-01"
}
```

---

### ✏️ Mettre à jour une commande

**URL :** `PUT /api/orders/{order}`

**Champs acceptés (optionnels) :**

- `user_id` (integer, doit exister dans users)  
- `total_amount` (float, ≥ 0)  
- `status` (string, doit être une valeur dans `Order::STATUSES`)  
- `order_date` (date)

**Réponse (succès) :**
```json
{
  "id": 1,
  "user_id": 1,
  "total_amount": 120.0,
  "status": "confirmed",
  "order_date": "2025-08-02"
}
```

---

### 🗑️ Supprimer une commande

**URL :** `DELETE /api/orders/{order}`

**Réponse (succès) :**
```json
{
  "message": "Order deleted successfully"
}
```
## 🔖 API Gestion des Tags

### Contrôleur : `TagController`

---

### ➕ Attacher un tag à un contenu (post ou produit)

**URL :** `POST /api/tags/attach`

**Champs requis :**

- `tag_name` (string, requis, max:255) — Nom du tag à attacher (le tag est créé s’il n’existe pas)  
- `taggable_id` (integer, requis) — ID de l’objet (post ou produit) auquel attacher le tag  
- `taggable_type` (string, requis) — Type de l’objet, valeur possible : `post` ou `product`

**Description :**  
Attache un tag à un post ou un produit en évitant les doublons.

**Réponse (succès) :**
```json
{
  "message": "Tag attaché avec succès.",
  "tag": {
    "id": 5,
    "tag_name": "exemple"
  }
}
```

---

### ➖ Détacher un tag d’un contenu (post ou produit)

**URL :** `POST /api/tags/detach`

**Champs requis :**

- `tag_id` (integer, requis, doit exister dans la table tags) — ID du tag à détacher  
- `taggable_id` (integer, requis) — ID de l’objet (post ou produit) dont on veut retirer le tag  
- `taggable_type` (string, requis) — Type de l’objet, valeur possible : `post` ou `product`

**Description :**  
Détache un tag d’un post ou produit. Retourne une erreur 404 si la relation n’existe pas.

**Réponse (succès) :**
```json
{
  "message": "Tag détaché avec succès."
}
```

**Réponse (relation inexistante) :**
```json
{
  "message": "Aucune relation trouvée entre ce contenu et ce tag."
}
```
Status HTTP : 404

---

## 📁 API Catégories

### Contrôleur : `CategoryController`

---

### 📋 Liste des catégories

**URL :** `GET /api/categories`  
**Description :** Récupère toutes les catégories.

**Réponse (succès) :**
```json
[
  {
    "id": 1,
    "name": "Catégorie A"
  },
  {
    "id": 2,
    "name": "Catégorie B"
  }
]
```

---

### 🔍 Afficher une catégorie avec ses sous-catégories

**URL :** `GET /api/categories/{id}`  
**Description :** Récupère une catégorie par son ID, avec ses sous-catégories associées.

**Réponse (succès) :**
```json
{
  "id": 1,
  "name": "Catégorie A",
  "subcategories": [
    {
      "id": 10,
      "name": "Sous-catégorie 1",
      "category_id": 1
    },
    {
      "id": 11,
      "name": "Sous-catégorie 2",
      "category_id": 1
    }
  ]
}
```

**Réponse (échec - catégorie non trouvée) :**
```json
{
  "message": "Catégorie non trouvée"
}
```
Statut HTTP : `404`

---

### ➕ Créer une nouvelle catégorie

**URL :** `POST /api/categories`  
**Description :** Crée une nouvelle catégorie.

**Paramètres requis (JSON ou form-data) :**
- `name` (string, requis, max 255)

**Réponse (succès) :**
```json
{
  "message": "Catégorie créée avec succès",
  "category": {
    "id": 3,
    "name": "Nouvelle Catégorie"
  }
}
```

**Code HTTP :** `201 Created`

---

### ✏️ Mettre à jour une catégorie

**URL :** `PUT /api/categories/{id}`  
**Description :** Met à jour le nom d'une catégorie.

**Paramètres (JSON ou form-data) :**
- `name` (string, optionnel, max 255)

**Réponse (succès) :**
```json
{
  "message": "Catégorie mise à jour avec succès",
  "category": {
    "id": 1,
    "name": "Nom mis à jour"
  }
}
```

**Réponse (échec - catégorie non trouvée) :**
```json
{
  "message": "Catégorie non trouvée"
}
```
Statut HTTP : `404`

---

### 🗑️ Supprimer une catégorie

**URL :** `DELETE /api/categories/{id}`  
**Description :** Supprime une catégorie existante.

**Réponse (succès) :**
```json
{
  "message": "Catégorie supprimée avec succès"
}
```

**Réponse (échec - catégorie non trouvée) :**
```json
{
  "message": "Catégorie non trouvée"
}
```
Statut HTTP : `404`

---




## 📂 API Sous-catégories

### Contrôleur : `SubcategoryController`

---

### 📋 Liste des sous-catégories

**URL :** `GET /api/subcategories`

**Description :**  
Retourne la liste de toutes les sous-catégories avec leur catégorie associée.

**Réponse (succès) :**
```json
[
  {
    "id": 1,
    "name": "Sous-catégorie A",
    "category": {
      "id": 5,
      "name": "Catégorie X"
    }
  },
  {
    "id": 2,
    "name": "Sous-catégorie B",
    "category": {
      "id": 3,
      "name": "Catégorie Y"
    }
  }
]
```

---

### ➕ Créer une nouvelle sous-catégorie

**URL :** `POST /api/subcategories`

**Paramètres (JSON ou form-data) :**

- `name` (string, requis, max 255)
- `category_id` (integer, requis, doit exister dans la table `categories`)

**Réponse (succès) :**
```json
{
  "message": "Sous-catégorie créée avec succès.",
  "subcategory": {
    "id": 10,
    "name": "Nouvelle sous-catégorie",
    "category_id": 5
  }
}
```

---

### 🔍 Afficher une sous-catégorie spécifique

**URL :** `GET /api/subcategories/{id}`

**Description :**  
Retourne la sous-catégorie avec sa catégorie associée.

**Réponse (succès) :**
```json
{
  "id": 10,
  "name": "Sous-catégorie A",
  "category": {
    "id": 5,
    "name": "Catégorie X"
  }
}
```

**Réponse (sous-catégorie non trouvée) :**
```json
{
  "message": "Sous-catégorie non trouvée"
}
```
Statut HTTP : 404

---

### ✏️ Mettre à jour une sous-catégorie

**URL :** `PUT /api/subcategories/{id}`

**Paramètres (JSON ou form-data) :**

- `name` (string, optionnel, max 255)
- `category_id` (integer, optionnel, doit exister dans la table `categories`)

**Réponse (succès) :**
```json
{
  "message": "Sous-catégorie mise à jour avec succès.",
  "subcategory": {
    "id": 10,
    "name": "Nom mis à jour",
    "category_id": 6
  }
}
```

**Réponse (sous-catégorie non trouvée) :**
```json
{
  "message": "Sous-catégorie non trouvée"
}
```
Statut HTTP : 404

---

### 🗑️ Supprimer une sous-catégorie

**URL :** `DELETE /api/subcategories/{id}`

**Réponse (succès) :**
```json
{
  "message": "Sous-catégorie supprimée avec succès."
}
```

**Réponse (sous-catégorie non trouvée) :**
```json
{
  "message": "Sous-catégorie non trouvée"
}
```
Statut HTTP : 404

---

