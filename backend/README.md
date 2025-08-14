# ✨ TaskFlow - Gestionnaire de Tâches Moderne

<div align="center">
  
  ![TaskFlow Logo](https://img.shields.io/badge/TaskFlow-Gestionnaire%20de%20T%C3%A2ches-6366f1?style=for-the-badge&logo=task&logoColor=white)
  
  **Une application de gestion de tâches élégante avec une interface glassmorphism**
  
  [![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
  [![Vue.js](https://img.shields.io/badge/Vue.js-3.x-4FC08D?style=flat-square&logo=vue.js&logoColor=white)](https://vuejs.org)
  [![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.x-38B2AC?style=flat-square&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
  [![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)](LICENSE)
  
</div>

---

## 🚀 À Propos

**TaskFlow** est une application moderne de gestion de tâches qui combine la puissance de Laravel avec l'élégance de Vue.js. Dotée d'une interface utilisateur époustouflante utilisant l'effet glassmorphism, elle offre une expérience visuelle immersive avec des animations fluides et des dégradés colorés.

### ✨ Caractéristiques Principales

- 🎨 **Interface Glassmorphism** - Design moderne avec effets de transparence et de flou
- 📋 **Gestion Complète des Tâches** - Créer, modifier, supprimer et organiser vos tâches
- 🔄 **Vue Kanban & Liste** - Basculez entre différents modes d'affichage
- 🔔 **Système de Notifications** - Restez informé des changements importantes
- 💬 **Commentaires sur les Tâches** - Collaborez efficacement avec votre équipe
- 📊 **Statistiques en Temps Réel** - Suivez vos progrès avec des métriques détaillées
- 🌈 **Animations Fluides** - Expérience utilisateur premium avec des transitions élégantes
- 📱 **Design Responsive** - Parfaitement adapté à tous les appareils

---

## 🛠️ Technologies Utilisées

### Backend
- **Laravel 11.x** - Framework PHP moderne et élégant
- **MySQL/PostgreSQL** - Base de données relationnelle
- **Laravel Sanctum** - Authentification API sécurisée
- **Queue Jobs** - Traitement asynchrone des tâches

### Frontend
- **Vue.js 3** - Framework JavaScript progressif
- **Vue Router** - Gestion des routes côté client
- **Pinia** - Gestion d'état moderne pour Vue
- **TailwindCSS** - Framework CSS utilitaire
- **Heroicons** - Icônes SVG élégantes

### Fonctionnalités Visuelles
- **Glassmorphism UI** - Effets de transparence et de flou
- **Gradient Backgrounds** - Arrière-plans colorés animés
- **Smooth Animations** - Transitions et animations fluides
- **Responsive Design** - Interface adaptative

---

## 📦 Installation

### Prérequis

- PHP 8.2+
- Node.js 18+
- Composer
- MySQL/PostgreSQL

### Étapes d'Installation

1. **Cloner le Repository**
   ```bash
   git clone https://github.com/votre-username/taskflow.git
   cd taskflow
   ```

2. **Installation des Dépendances Backend**
   ```bash
   composer install
   ```

3. **Configuration de l'Environnement**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configuration de la Base de Données**
   ```bash
   # Éditez le fichier .env avec vos paramètres DB
   php artisan migrate --seed
   ```

5. **Installation des Dépendances Frontend**
   ```bash
   npm install
   npm run build
   ```

6. **Lancement du Serveur**
   ```bash
   php artisan serve
   npm run dev  # Dans un autre terminal pour le développement
   ```

---

## 🎯 Utilisation

### Démarrage Rapide

1. **Connexion** - Accédez à l'application et connectez-vous
2. **Créer une Tâche** - Cliquez sur "Nouvelle tâche" pour commencer
3. **Organiser** - Utilisez la vue Kanban pour organiser vos tâches
4. **Collaborer** - Ajoutez des commentaires pour collaborer
5. **Suivre** - Consultez les statistiques pour suivre vos progrès

### Fonctionnalités Avancées

- **Priorités de Tâches** - Définissez des priorités (Basse, Moyenne, Haute)
- **Statuts Personnalisés** - À faire, En cours, Terminé, Annulé
- **Échéances** - Définissez des dates limites pour vos tâches
- **Notifications** - Recevez des alertes pour les tâches importantes

---

## 🎨 Aperçu de l'Interface

### Page de Connexion
Interface glassmorphism avec arrière-plan gradient animé et effets de transparence.

### Tableau de Bord
Vue d'ensemble avec statistiques en temps réel et liste des tâches récentes.

### Vue Kanban
Organisation visuelle des tâches avec glisser-déposer intuitif.

### Détail des Tâches
Interface complète pour gérer tous les aspects d'une tâche.

---

## 🔧 Configuration Avancée

### Variables d'Environnement

```env
# Application
APP_NAME="TaskFlow"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votre-domaine.com

# Base de Données
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=taskflow
DB_USERNAME=votre_utilisateur
DB_PASSWORD=votre_mot_de_passe

# Queue (optionnel)
QUEUE_CONNECTION=database
```

### Optimisations de Production

```bash
# Optimisation Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Build de production frontend
npm run build
```

---

## 📚 API Documentation

### Endpoints Principaux

- `GET /api/tasks` - Liste des tâches
- `POST /api/tasks` - Créer une tâche
- `PUT /api/tasks/{id}` - Modifier une tâche
- `DELETE /api/tasks/{id}` - Supprimer une tâche
- `GET /api/notifications` - Notifications utilisateur

### Authentification

L'API utilise Laravel Sanctum pour l'authentification. Incluez le token dans l'en-tête :

```javascript
headers: {
  'Authorization': 'Bearer ' + token,
  'Accept': 'application/json'
}
```

---

## 🤝 Contribution

Nous accueillons les contributions ! Voici comment participer :

1. **Fork** le projet
2. **Créez** une branche pour votre fonctionnalité (`git checkout -b feature/nouvelle-fonctionnalite`)
3. **Commitez** vos changements (`git commit -m 'Ajout d'une nouvelle fonctionnalité'`)
4. **Push** vers la branche (`git push origin feature/nouvelle-fonctionnalite`)
5. **Ouvrez** une Pull Request
---

---

## 🙏 Remerciements

- **Laravel Team** - Pour ce framework exceptionnel
- **Vue.js Team** - Pour l'écosystème Vue innovant
- **TailwindCSS** - Pour le framework CSS utilitaire
- **Heroicons** - Pour les icônes élégantes
---