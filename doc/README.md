# Documentació Tècnica de DeliShare

## Objectius
DeliShare és una xarxa social centrada en compartir i descobrir receptes. Els objectius principals són:
* Facilitar la compartició de receptes entre usuaris
* Crear una comunitat de cuiners i amants de la gastronomia
* Ofereix funcionalitats en temps real com xat i transmissions en directe
* Proporcionar una experiència d'usuari intuïtiva i atractiva
* Generar receptes automàtiques utilitzant IA

## Arquitectura bàsica

### Tecnologies utilitzades
* **Frontend:**
  * Vue.js 3 amb Vite
  * Tailwind CSS per estils
  * Pinia per gestió d'estat
  * Socket.IO Client per funcionalitats en temps real
  * Groq (IA) per generació de receptes
  * Gemini (IA) per validació de imatges i videos al moment de crear una recepte


* **Backend:**
  * Laravel (PHP) per API REST
  * MySQL 8.2 per base de dades
  * Node.js amb Socket.IO per API en temps real

### Interrelació entre components
```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│   Frontend  │     │   Backend   │     │    Node.js  │
│   (Vue.js)  │◄───►│  (Laravel)  │◄───►│  (Socket.IO)│
└─────────────┘     └─────────────┘     └─────────────┘
       ▲                   ▲                   ▲
       │                   │                   │
       ▼                   ▼                   ▼
┌─────────────────────────────────────────────────────┐
│                   Base de Dades                     │
│                     (MySQL)                         │
└─────────────────────────────────────────────────────┘
```

## Entorn de desenvolupament

### Requisits previs
* Docker i Docker Compose
* Node.js 18+
* PHP 8.2+
* Composer
* Git(o terminal)

### Configuració
1. Clonar el repositori:
```bash
git clone [URL_DEL_REPOSITORIO]
cd delishare
```

2. Configurar variables d'entorn:
```bash
# Frontend
cp front/.env.example front/.env

# Backend
cp back/.env.example back/.env

# Node.js
cp node/.env.example node/.env
```

3. Iniciar els serveis:
```bash
docker-compose up
```



## Desplegament a producció

### Requisits del servidor
* Servidor Linux (Ubuntu 20.04+)
* Docker i Docker Compose
* Certificat SSL
* Domini configurat

### Passos de desplegament
1. Configurar el servidor:
```bash
# Instal·lar Docker
curl -fsSL https://get.docker.com | sh

# Instal·lar Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/download/v2.20.0/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
```

2. Desplegar l'aplicació:
```bash
# Clonar el repositorio
git clone [URL_DEL_REPOSITORIO]
cd delishare

# Configurar variables d'entorn de producció
cp .env.production .env

# Iniciar serveis
docker-compose -f docker-compose.prod.yml up -d
```


### Interrelació entre components
```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│   Frontend  │     │   Backend   │     │    Node.js  │
│   (Vue.js)  │◄───►│  (Laravel)  │◄───►│  (Socket.IO)│
└─────────────┘     └─────────────┘     └─────────────┘
       ▲                   ▲                   ▲
       │                   │                   │
       ▼                   ▼                   ▼
┌─────────────────────────────────────────────────────┐
│                   Base de Dades                     │
│                     (MySQL)                         │
└─────────────────────────────────────────────────────┘
```

## API Endpoints

### Autenticació i Usuari
| Mètode | Ruta | Controlador | Funció |
|--------|------|-------------|----------|
| POST | `/register` | AuthController | register |
| POST | `/login` | AuthController | login |
| GET | `/userInfo/{userId}` | AuthController | getUserInfo |
| POST | `/logout` | AuthController | logout |
| POST | `/updatePerfile` | AuthController | updatePerfil |
| PUT | `/updateProfilePicture` | AuthController | updateProfilePicture |
| POST | `/cambiarContra` | AuthController | cambiarContra |
| PUT | `/usuarios/{id}/rol` | AuthController | cambiarRol |

### Usuaris
| Mètode | Ruta | Controlador | Funció |
|--------|------|-------------|----------|
| GET | `/users` | UserController | index |
| GET | `/users/{id}` | UserController | show |
| DELETE | `/users/{id}` | UserController | destroy |
| POST | `/users/` | UserController | store |
| GET | `/getAllUsers` | RecipeController | getAllUsers |
| POST | `/send-verification` | UserController | sendVerificationEmail |

### Receptes
| Mètode | Ruta | Controlador | Funció |
|--------|------|-------------|----------|
| GET | `/recipes` | RecipeController | index |
| GET | `/recipes/{id}` | RecipeController | show |
| POST | `/recipes` | RecipeController | store |
| PUT | `/recipes/{id}` | RecipeController | update |
| DELETE | `/recipes/{id}` | RecipeController | destroy |
| GET | `/getAllRecipes` | RecipeController | getAllRecipes |
| GET | `/user/recetas` | RecipeController | getRecipesByUser |
| GET | `/recipes/{id}/steps` | RecipeController | getRecipeSteps |
| GET | `/recipes/{id}/download` | RecipeController | downloadFullRecipe |
| GET | `/ingredients` | RecipeController | getAllIngredients |
| POST | `/recipes/filter-by-ingredients` | RecipeController | filterByIngredients |

### Categories
| Mètode | Ruta | Controlador | Funció |
|--------|------|-------------|----------|
| POST | `/categories` | CategoryController | store |
| GET | `/categories` | CategoryController | index |
| GET | `/categories/{id}` | CategoryController | show |
| PUT | `/categories/{id}` | CategoryController | update |
| DELETE | `/categories/{id}` | CategoryController | destroy |
| GET | `/filterByCategory/{id}` | RecipeController | filterByCategory |

### Cuines
| Mètode | Ruta | Controlador | Funció |
|--------|------|-------------|----------|
| POST | `/cuisines` | CuisineController | store |
| GET | `/cuisines` | CuisineController | index |
| GET | `/cuisines/{id}` | CuisineController | show |
| PUT | `/cuisines/{id}` | CuisineController | update |
| DELETE | `/cuisines/{id}` | CuisineController | destroy |
| GET | `/filterByCuisine/{id}` | CuisineController | filterByCuisine |

### Comentaris
| Mètode | Ruta | Controlador | Funció |
|--------|------|-------------|----------|
| POST | `/recipes/{id}/comment` | RecipeController | addComment |
| GET | `/recipes/{id}/comments` | RecipeController | getRecipeComments |
| DELETE | `/recipes/{recipeId}/comments` | RecipeController | deleteCommentByText |
| GET | `/comments` | RecipeController | getAllComments |

### M'agrada i Guardats
| Mètode | Ruta | Controlador | Funció |
|--------|------|-------------|----------|
| POST | `/recipes/{recipe}/like` | RecipeController | toggleLike |
| GET | `/recipes/{recipe}/likes` | RecipeController | getLikes |
| POST | `/recipes/{id}/like` | RecipeController | likeRecipe |
| POST | `/recipes/{id}/unlike` | RecipeController | unlikeRecipe |
| GET | `/saved-recipes` | SavedRecipeController | index |
| POST | `/saved-recipes/toggle/{recipeId}` | SavedRecipeController | toggleSave |

### Carpetes
| Mètode | Ruta | Controlador | Funció |
|--------|------|-------------|----------|
| POST | `/folders` | FolderController | store |
| POST | `/folders/{folderId}/recipes/{recipeId}` | FolderController | addRecipe |
| GET | `/folders/{folder}` | FolderController | show |
| DELETE | `/folders/{folderId}/recipes/{recipeId}` | FolderController | removeRecipe |
| DELETE | `/folders/{folderId}` | FolderController | removeFolder |
| GET | `/folders` | FolderController | index |
| GET | `/folders/{folder}/recipes` | FolderController | getRecipes |

### Lives
| Mètode | Ruta | Controlador | Funció |
|--------|------|-------------|----------|
| GET | `/lives` | LiveController | index |
| POST | `/lives` | LiveController | store |
| GET | `/lives/{live}` | LiveController | show |
| PUT | `/lives/{live}` | LiveController | update |
| DELETE | `/lives/{live}` | LiveController | destroy |
| GET | `/mis-lives` | LiveController | chefLives |
| GET | `/users/{userId}/lives` | LiveController | getUserLives |

### Notificacions
| Mètode | Ruta | Controlador | Funció |
|--------|------|-------------|----------|
| GET | `/notifications` | NotificationController | getUserNotifications |
| POST | `/notifications` | NotificationController | createNotification |
| PUT | `/notifications/{id}/read` | NotificationController | markAsRead |

### Recomanacions
| Mètode | Ruta | Controlador | Funció |
|--------|------|-------------|----------|
| GET | `/recommendations/preferences` | RecommendationController | getUserPreferences |
| POST | `/recommendations/preferences` | RecommendationController | storePreferences |
| GET | `/user/preferences` | RecommendationController | getPreferenceNames |
| GET | `/recipes/recommended` | RecipeController | getRecommendedRecipes |
| GET | `/recommendations/options` | RecommendationController | getCuisinesAndCategories |
| GET | `/user/liked-recipes` | RecipeController | getUserLikedRecipes |

### Filtres
| Mètode | Ruta | Controlador | Funció |
|--------|------|-------------|----------|
| GET | `/filterByTime/{time}` | RecipeController | filterByTime |
| GET | `/times` | RecipeController | getAllTimes |

> **Nota**: Totes les rutes estan protegides amb el middleware `auth:sanctum` excepte les que són explícitament públiques com el registre, login i algunes rutes de visualització.

## Estructura de la Base de Dades

### Taules Principals

#### Users
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    profile_picture VARCHAR(255),
    role ENUM('user', 'admin', 'chef') DEFAULT 'user',
    email_verified_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

#### Recipes
```sql
CREATE TABLE recipes (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    prep_time INT,
    cook_time INT,
    servings INT,
    difficulty ENUM('fàcil', 'mitjà', 'difícil'),
    image VARCHAR(255),
    calories INT,
    protein DECIMAL(5,2),
    carbs DECIMAL(5,2),
    fats DECIMAL(5,2),
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

#### Categories
```sql
CREATE TABLE categories (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

#### Cuisines
```sql
CREATE TABLE cuisines (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

### Taules de Relació

#### Recipe_Category
```sql
CREATE TABLE recipe_category (
    recipe_id BIGINT UNSIGNED NOT NULL,
    category_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (recipe_id, category_id),
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);
```

#### Recipe_Cuisine
```sql
CREATE TABLE recipe_cuisine (
    recipe_id BIGINT UNSIGNED NOT NULL,
    cuisine_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (recipe_id, cuisine_id),
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
    FOREIGN KEY (cuisine_id) REFERENCES cuisines(id) ON DELETE CASCADE
);
```

#### Ingredients
```sql
CREATE TABLE ingredients (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    recipe_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    quantity DECIMAL(10,2),
    unit VARCHAR(50),
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE
);
```

#### Steps
```sql
CREATE TABLE steps (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    recipe_id BIGINT UNSIGNED NOT NULL,
    step_number INT NOT NULL,
    description TEXT NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE
);
```

### Taules de Social

#### Likes
```sql
CREATE TABLE likes (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    recipe_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE
);
```

#### Comments
```sql
CREATE TABLE comments (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    recipe_id BIGINT UNSIGNED NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE
);
```

#### Saved_Recipes
```sql
CREATE TABLE saved_recipes (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    recipe_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE
);
```

### Taules de Carpetes

#### Folders
```sql
CREATE TABLE folders (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

#### Folder_Recipe
```sql
CREATE TABLE folder_recipe (
    folder_id BIGINT UNSIGNED NOT NULL,
    recipe_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    PRIMARY KEY (folder_id, recipe_id),
    FOREIGN KEY (folder_id) REFERENCES folders(id) ON DELETE CASCADE,
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE
);
```

### Taules de Lives

#### Lives
```sql
CREATE TABLE lives (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    scheduled_at TIMESTAMP NOT NULL,
    status ENUM('scheduled', 'live', 'ended') DEFAULT 'scheduled',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### Taules de Notificacions

#### Notifications
```sql
CREATE TABLE notifications (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    type VARCHAR(50) NOT NULL,
    content TEXT NOT NULL,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### Taules de Preferències

#### User_Preferences
```sql
CREATE TABLE user_preferences (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    preference_type VARCHAR(50) NOT NULL,
    preference_value VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### Índexs i Restriccions
* Totes les taules tenen timestamps `created_at` i `updated_at`
* Les claus foranes tenen `ON DELETE CASCADE` per mantenir la integritat referencial
* Els emails d'usuari són únics
* Els noms de categories i cuisines són únics
* Els passos de les receptes tenen un número d'ordre únic per recepta

## Estructura Frontend

### Pàgines Principals

1. **Pàgina Landing**
   - Pantalla principal amb la millor experiència d'usuari
   - Integració amb notificacions en temps real
   - Components:
     - Hero Section (Benvinguda i presentació)
     - Featured Recipes (Receptes destacades)
     - User Stats (Estadístiques de l'usuari)
     - Notification Center (Centro de notificacions)

2. **Pàgina de Búsqueda**
   - Motor de cerca avançat amb múltiples criteris
   - Filtres dinàmics en temps real
   - Resultats amb paginació infinita
   - Components:
     - Barra de cerca intel·ligent
     - Barra lateral de filtres
     - Targetes de receptes
     - Sistema de paginació

3. **Pàgina de Creació de Recepta**
   - Formulari complet per crear una nova recepta
   - Components:
     - Camps bàsics (títol, descripció, temps de preparació)
     - Gestió d'ingredients (afegir, eliminar, quantitats)
     - Pasos de preparació (ordre, descripció)
     - Gestió de imatge i video (carregar, previsualitzar)
     - Selecció de categories i cuisines
     - Informació nutricional 

4. **Pàgina Live**
   - Transmissions en directe de cuina
   - Chat integrat amb emojis
   - Interacció en temps real amb els cuiners
   - Components:
     - Reproductor de vídeo en directe
     - Sala de xat amb emojis
     - Controls d'usuari
     - Estadístiques en directe

5. **Pàgina de Perfil**
   - Gestió completa del perfil d'usuari
   - Sistema CRUD per les receptes
   - Estadístiques personalitzades
   - Components:
     - Capçalera del perfil
     - Gestió de receptes
     - Estadístiques d'usuari
     - Configuració del compte

6. **Detalls de Recepta**
   - Visualització completa d'una recepta
   - Components:
     - Header de la recepta
     - Llista d'ingredients
     - Pasos de preparació
     - Informació nutricional
     - Comentaris d'usuaris
     - Botons d'interacció (like, guardar, compartir)

### Fluxos de Comunicació

```

                          ┌─────────────────────────────┐
                          │        NAVBAR PRINCIPAL     │
                          └────────────┬────────────────┘
                                       │
          ┌────────────────────────────┼────────────────────────────┐
          │                            │                            │
  ┌───────▼────────┐         ┌─────────▼─────────┐        ┌─────────▼─────────┐
  │ 1. Landing Page│         │ 2. Search Page    │        │ 3. Afegir Recepta │
  └────────────────┘         └───────────────────┘        └───────────────────┘
          │                            │                            │
          │                            │                            │
          ▼                            ▼                            ▼
 (Contingut dinàmic:         (Barra de cerca, filtres     (Formulari assistit per
 populars, recents, IA)      i receptes filtrades)        IA i pujada de contingut)


          ┌────────────────────────────┴────────────────────────────┐
          │                                                         │
  ┌───────▼────────┐                                         ┌──────▼─────────┐
  │ 4. Lives Page  │                                         │ 5. Perfil Page │
  └────────────────┘                                         └────────────────┘
          │                                                         │
   (Veure lives en viu,                                   (Informació personal,
    xat, programació)                                     receptes, guardats,
                                                                carpetes, etc.)

```

### Fluxos d'Interacció

1. **Flux de Notificacions**
```
┌──────────────────────────────────────────────────────────────────────────────┐
│                                   Landing                                    │
│                                                                              │
│  ┌────────────────┐    ┌────────────────┐    ┌────────────────┐    ┌─────────┐│
│  │Nova notificació│───►│Notificacions   │───►│ Actualització  │───►│ Alerta  ││
│  │    rebuda      │    │    en temps    │    │                │    │  visual ││
│  └────────────────┘    │     real       │    └────────────────┘    └─────────┘│
│                        └────────────────┘                                    │
└──────────────────────────────────────────────────────────────────────────────┘
```

2. **Flux de Gestió de Receptes**
```
┌──────────────────────────────────────────────────────────────────────────────┐
│                                   Perfil                                     │
│                                                                              │
│  ┌────────────────┐    ┌────────────────┐    ┌────────────────┐    ┌─────────┐│
│  │ Nova recepta   │───►│ Gestió de      │───►│ Actualització  │───►│ UI      ││
│  │    creada      │    │  receptes      │    │                │    │  actual. ││
│  └────────────────┘    │    (CRUD)      │    └────────────────┘    └─────────┘│
│                        └────────────────┘                                    │
└──────────────────────────────────────────────────────────────────────────────┘
```

3. **Flux de Búsqueda**
```
┌──────────────────────────────────────────────────────────────────────────────┐
│                                   Búsqueda                                   │
│                                                                              │
│  ┌────────────────┐    ┌────────────────┐    ┌────────────────┐    ┌─────────┐│
│  │ Criteris de    │───►│ Filtratge en   │───►│ Resultats en   │───►│ Paginació│
│  │    cerca       │    │     temps real │    │     temps real │    │ infinita││
│  └────────────────┘    └────────────────┘    └────────────────┘    └─────────┘│
└──────────────────────────────────────────────────────────────────────────────┘
```

4. **Flux de Transmissió en Directe**
```
┌──────────────────────────────────────────────────────────────────────────────┐
│                                   Live                                       │
│                                                                              │
│  ┌────────────────┐    ┌────────────────┐    ┌────────────────┐    ┌─────────┐│
│  │ Inici de trans.│───►│ Streaming en   │───►│ Xat en temps   │───►│ Controls││
│  │    missió      │    │     temps real │    │     real       │    │  d'usuari││
│  └────────────────┘    └────────────────┘    └────────────────┘    └─────────┘│
└──────────────────────────────────────────────────────────────────────────────┘
```

## Funcionalitats principals

### Generació de receptes amb IA
* Utilitza Groq (llama-3.3-70b-versatile)
* Genera receptes completes amb ingredients i passos
* Calcula valors nutricionals
* Adapta les receptes segons preferències

### Xat en temps real
* Implementat amb Socket.IO
* Suporta múltiples sales de xat
* Notificacions en temps real
* Historial de missatges

### Transmissions en directe
* Streaming de vídeo
* Interacció en temps real
* Sistema de comentaris
* Gravació de sessions

## Integració amb Intel·ligència Artificial

### Configuració de Groq
```javascript
const groq = new Groq({
  apiKey: import.meta.env.VITE_GROQ_API_KEY,
  dangerouslyAllowBrowser: true,
});
```

### Prompt de Generació de Receptes
```javascript
const userMessage = ` 
ESTRICTE: Només pots generar receptes de cuina. Rebutja absolutament qualsevol altre tipus de sol·licitud.

Genera UNA RECEPTA DE CUINA en format JSON basada en aquestes dades:
- Categoria: ${categoryName}
- Cuina: ${cuisineName}
- Racions: ${servings}
- Títol: ${recipe.value.title || "[crea un títol atractiu]"}
- Explicació del que vol fer l'usuari: ${recipe.value.explanation}

La recepta ha de ser EXCLUSIVAMENT sobre cuina i ha d'incloure:
1. Descripció breu (màxim 2 frases)
2. Llista d'ingredients amb quantitats exactes
3. Passos de preparació numerats
4. Informació nutricional per ració
5. Temps de preparació i cocció

FORMAT REQUERIT (en català, només JSON):
{
  "title": "Títol de la recepta",
  "description": "Descripció breu",
  "ingredients": [
    {"quantity": "quantitat exacta", "unit": "unitat (obligatori)", "name": "Nom ingredient"}
  ],
  "steps": [
    "Descripció detallada de pas 1",
    "Descripció detallada de pas 2"
  ],
  "nutrition": {
    "calories": 0,
    "protein": 0,
    "fats": 0,
    "carbs": 0
  },
  "times": {
    "prep_time": 0,
    "cook_time": 0
  }
}

NORMES ESTRICTES:
- ABSOLUTAMENT RES que no sigui sobre cuina/receptes
- No inclour cap tipus de contingut inadequat
- Quantitats precises i reals
- Passos clars i executables
- Informació nutricional realista
- Resposta EXCLUSIVAMENT en format JSON vàlid`;
```

### Exemple de Petició i Resposta
```javascript
// Petició a la IA
const response = await groq.chat.completions.create({
  model: "llama-3.3-70b-versatile",
  messages: [{
    role: "user", "amdin", "chef",
    content: userMessage
  }],
  response_format: { type: "json_object" },
  temperature: 0.5,
});

// Exemple de resposta
{
  "title": "Paella de Marisc",
  "description": "Una deliciosa paella tradicional valenciana amb marisc fresc i arròs bomba.",
  "ingredients": [
    {"quantity": "400", "unit": "g", "name": "arròs bomba"},
    {"quantity": "200", "unit": "g", "name": "gambes"},
    {"quantity": "200", "unit": "g", "name": "musclos"},
    {"quantity": "1", "unit": "unitat", "name": "ceba"},
    {"quantity": "2", "unit": "unitats", "name": "tomàquets"}
  ],
  "steps": [
    "Sofregir la ceba i el tomàquet en l'oli d'oliva",
    "Afegir l'arròs i remenar durant 2 minuts",
    "Incorporar el brou calent i deixar coure 18 minuts",
    "Afegir el marisc i deixar reposar 5 minuts"
  ],
  "nutrition": {
    "calories": 450,
    "protein": 25,
    "fats": 15,
    "carbs": 60
  },
  "times": {
    "prep_time": 20,
    "cook_time": 25
  }
}
```

### Paràmetres de la IA
* **Model**: llama-3.3-70b-versatile
* **Temperature**: 0.5 (equilibri entre creativitat i consistència)
* **Format**: JSON object
* **Idioma**: Català
* **Context**: Cuina i receptes

## Guia de contribució
1. Fork del repositorio
2. Crear branca per feature
3. Commit dels canvis
4. Push a la branca
5. Crear Pull Request

## Estàndards de codi
* Frontend: ESLint + Prettier
* Backend: PSR-12
* Node.js: ESLint + Prettier
* Commits: Conventional Commits

## Monitoreig i logs
* Logs d'aplicació: `/var/log/delishare/`
* Monitoreig: Prometheus + Grafana
* Alertes: Telegram Bot

## Seguretat
* Autenticació JWT
* HTTPS obligatori
* Validació d'entrada
* Protecció contra CSRF
* Rate limiting

## Manteniment
* Backups diaris
* Actualitzacions de seguretat
* Monitoreig de rendiment
* Neteja de logs


## Validació d’Imatges i Vídeos amb Gemini

Aquest servei permet validar si una imatge o un vídeo conté menjar, utilitzant el model Gemini 1.5 Flash de Google a través de la seva API oficial (`@google/generative-ai`).

### Validació d’Imatges

El procés de validació d’imatges es basa a convertir el fitxer a base64 i enviar-lo al model Gemini juntament amb un prompt redactat en català. Aquest prompt sol·licita al model que actuï com un expert en gastronomia i analitzi si la imatge mostra menjar, ingredients, plats cuinats o persones menjant. La resposta esperada és un JSON amb dos camps: `isFood` (booleà que indica si hi ha menjar o no) i `reason` (explicació en català).

**Prompt clau:**

`Eres un experto en análisis de imágenes de comida. Tu tarea es determinar si la imagen proporcionada contiene comida o no. 
    
    Devuelve un JSON con el siguiente formato:
    {
      "isFood": boolean,
      "reason": "string" // Explicación en catalán
    }
    
    Reglas:
    - Si la imagen muestra claramente comida (platos preparados, ingredientes, etc.), marca isFood como true
    - Si la imagen muestra personas pero están comiendo o con comida, marca isFood como true
    - Si la imagen no contiene comida (personas solas, paisajes, objetos, etc.), marca isFood como false
    - Si no puedes determinar con seguridad, marca isFood como false`


### Validació de Vídeos

La validació de vídeos es realitza extraient diversos fotogrames en moments específics (1, 5, 10, 15 i 20 segons). Cada fotograma es converteix a base64 i s’analitza individualment amb el mateix procés que en les imatges. Si almenys un dels fotogrames conté menjar, el vídeo es considera vàlid. El resultat també es retorna en format JSON amb un camp `isFood` i una `reason` explicant el veredicte.

**Prompt clau:**

`Ets un expert en vídeos de cuina. Aquesta imatge és un fotograma extret d'un vídeo. La teva tasca és determinar si mostra aliments o escenes relacionades amb la cuina.
Retorna un JSON amb el format següent:
`Ets un expert en vídeos de cuina. Aquesta imatge és un fotograma extret d'un vídeo. La teva tasca és determinar si mostra aliments o escenes relacionades amb la cuina.
  
  Retorna un JSON amb el format següent:
  {
    "isFood": boolean,
    "reason": "string" 
  }
  
  Recorda:
  - Si el fotograma mostra clarament menjar, ingredients o cuina, és vàlid.
  - Si no sembla rellevant a la cuina, és invàlid.`

  
### Output esperat

Tant per imatges com per vídeos:
```json
{
  "isFood": true | false,
  "reason": "explicació en català"
}

