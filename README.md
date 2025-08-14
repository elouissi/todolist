# 📝 To-Do List Full Stack Application

Une application de gestion de tâches en temps réel développée avec **Laravel** (Backend) et **Vue.js** (Frontend), incluant l'authentification JWT et les notifications en temps réel via WebSockets.

## 🚀 Technologies Utilisées

### Backend
- **Laravel** (PHP Framework)
- **PostgreSQL/MySQL** (Base de données)
- **JWT Auth** (Authentification)
- **Laravel Echo** (WebSockets)
- **Pusher** (Broadcasting)
- **Service & Repository Pattern**

### Frontend
- **Vue.js 3** (Framework JavaScript)
- **Pinia** (State Management)
- **Axios** (HTTP Client)
- **Laravel Echo** (WebSocket Client)
- **Pusher-js** (Real-time Communication)

## 📋 Fonctionnalités

### 🔐 Authentification
- Inscription avec informations complètes (nom, email, téléphone, adresse, image, mot de passe)
- Connexion avec JWT
- Protection des routes par middleware
- Isolation des données par utilisateur

### ✅ Gestion des Tâches (CRUD)
- Créer une nouvelle tâche
- Lister toutes les tâches de l'utilisateur
- Afficher les détails d'une tâche
- Modifier une tâche existante
- Supprimer une tâche

### 🔔 Notifications en Temps Réel
- Notification automatique à la création d'une tâche
- Page dédiée aux notifications
- Communication via WebSockets (Pusher)

## 🏗️ Architecture

Le projet suit les principes **SOLID** et implémente les patterns :
- **Service Pattern** : Logique métier séparée
- **Repository Pattern** : Abstraction de la couche de données
- **Event/Listener Pattern** : Gestion des événements
- **JWT Middleware** : Sécurisation des API

## 📁 Structure du Projet

```
todo-app/
├── backend/                 # Application Laravel
│   ├── app/
│   │   ├── Http/Controllers/
│   │   ├── Services/
│   │   ├── Repositories/
│   │   ├── Models/
│   │   ├── Events/
│   │   └── Listeners/
│   ├── database/migrations/
│   ├── routes/api.php
│   └── .env.example
├── frontend/                # Application Vue.js
│   ├── src/
│   │   ├── components/
│   │   ├── views/
│   │   ├── stores/
│   │   ├── services/
│   │   └── router/
│   ├── package.json
│   └── .env.example
└── README.md
```

## 🛠️ Installation et Configuration

### Prérequis
- PHP >= 8.1
- Composer
- Node.js >= 16.x
- NPM ou Yarn
- PostgreSQL ou MySQL
- Compte Pusher (pour les WebSockets)

### 1️⃣ Cloner le Repository

```bash
git clone https://github.com/votre-username/todo-app.git
cd todo-app
```

### 2️⃣ Configuration Backend (Laravel)

```bash
# Aller dans le dossier backend
cd backend

# Installer les dépendances PHP
composer install

# Copier le fichier d'environnement
cp .env.example .env

# Générer la clé d'application
php artisan key:generate

# Générer la clé JWT
php artisan jwt:secret
```

### 3️⃣ Configuration Base de Données

Modifier le fichier `.env` avec vos informations :

```env
DB_CONNECTION=pgsql  # ou mysql
DB_HOST=127.0.0.1
DB_PORT=5432        # ou 3306 pour MySQL
DB_DATABASE=todo_app
DB_USERNAME=votre_username
DB_PASSWORD=votre_password
```

Créer la base de données et exécuter les migrations :

```bash
# Créer la base de données (PostgreSQL)
createdb todo_app

# Ou pour MySQL
mysql -u root -p -e "CREATE DATABASE todo_app;"

# Exécuter les migrations
php artisan migrate
```

### 4️⃣ Configuration Pusher

Créer un compte sur [Pusher](https://pusher.com/) et ajouter les clés dans `.env` :

```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=votre_app_id
PUSHER_APP_KEY=votre_app_key
PUSHER_APP_SECRET=votre_app_secret
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=eu
```

### 5️⃣ Démarrer le Backend

```bash
# Démarrer le serveur Laravel
php artisan serve

# Dans un autre terminal, démarrer la queue pour les événements
php artisan queue:work
```

Le backend sera accessible sur `http://localhost:8000`

### 6️⃣ Configuration Frontend (Vue.js)

```bash
# Aller dans le dossier frontend
cd ../frontend

# Installer les dépendances Node.js
npm install

# Copier le fichier d'environnement
cp .env.example .env.local
```

Modifier `.env.local` :

```env
VITE_API_BASE_URL=http://localhost:8000/api
VITE_PUSHER_APP_KEY=votre_app_key
VITE_PUSHER_APP_CLUSTER=eu
```

### 7️⃣ Démarrer le Frontend

```bash
# Démarrer le serveur de développement
npm run dev
```

Le frontend sera accessible sur `http://localhost:5173`

## 🧪 Tests

### Backend
```bash
cd backend
php artisan test
```

### Frontend
```bash
cd frontend
npm run test
```

## 📡 API Endpoints

### Authentification
| Méthode | URL | Description |
|---------|-----|-------------|
| POST | `/api/auth/register` | Inscription |
| POST | `/api/auth/login` | Connexion |
| POST | `/api/auth/logout` | Déconnexion |
| GET | `/api/auth/me` | Profil utilisateur |

### Tâches (Routes protégées)
| Méthode | URL | Description |
|---------|-----|-------------|
| GET | `/api/tasks` | Liste des tâches |
| GET | `/api/tasks/{id}` | Détail d'une tâche |
| POST | `/api/tasks` | Créer une tâche |
| PUT | `/api/tasks/{id}` | Modifier une tâche |
| DELETE | `/api/tasks/{id}` | Supprimer une tâche |

### Exemple de Requête

```bash
# Connexion
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "password"
  }'

# Créer une tâche (avec token)
curl -X POST http://localhost:8000/api/tasks \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -d '{
    "title": "Ma nouvelle tâche",
    "description": "Description de la tâche",
    "status": "pending"
  }'
```

## 🔔 Notifications en Temps Réel

Les notifications sont envoyées automatiquement lors de la création d'une tâche :

1. **Backend** : Event `TaskCreated` déclenché
2. **Pusher** : Diffusion sur le canal `tasks.{user_id}`
3. **Frontend** : Réception via Laravel Echo
4. **UI** : Affichage dans la page notifications

## 🚀 Déploiement

### Production avec Docker

```bash
# Construire les images
docker-compose build

# Démarrer les services
docker-compose up -d

# Exécuter les migrations
docker-compose exec backend php artisan migrate
```

### Variables d'Environnement Production

N'oubliez pas de configurer :
- `APP_ENV=production`
- `APP_DEBUG=false`
- Base de données de production
- Clés Pusher de production
- Domaine et certificat SSL

## 🤝 Contribution

1. Fork le projet
2. Créer une branche (`git checkout -b feature/amazing-feature`)
3. Commit les changements (`git commit -m 'Add amazing feature'`)
4. Push sur la branche (`git push origin feature/amazing-feature`)
5. Ouvrir une Pull Request

## 📜 Licence

Ce projet est sous licence MIT. Voir le fichier [LICENSE](LICENSE) pour plus de détails.

## 👨‍💻 Auteur

**Votre Nom**
- GitHub: [@votre-username](https://github.com/votre-username)
- Email: votre.email@example.com

## 🆘 Support

Si vous rencontrez des problèmes :

1. Vérifiez que tous les services sont démarrés
2. Consultez les logs Laravel (`storage/logs/laravel.log`)
3. Vérifiez la console du navigateur pour les erreurs frontend
4. Ouvrez une issue sur GitHub

## 📚 Ressources Utiles

- [Documentation Laravel](https://laravel.com/docs)
- [Documentation Vue.js](https://vuejs.org/)
- [Documentation Pinia](https://pinia.vuejs.org/)
- [Documentation Laravel Echo](https://laravel.com/docs/broadcasting#client-side-installation)
- [Documentation Pusher](https://pusher.com/docs)

---

⭐ **N'hésitez pas à donner une étoile si ce projet vous a été utile !**
