# Configuration de l'authentification Facebook pour LibroLink

## Étapes de configuration

### 1. Créer une application Facebook

1. Allez sur [Facebook Developers](https://developers.facebook.com/)
2. Connectez-vous avec votre compte Facebook
3. Cliquez sur "Mes applications" puis "Créer une application"
4. Choisissez "Consommateur" comme type d'application
5. Remplissez les informations de base :
   - Nom de l'application : LibroLink
   - Email de contact : votre email
6. Créez l'application

### 2. Configurer Facebook Login

1. Dans le tableau de bord de votre application, ajoutez le produit "Facebook Login"
2. Configurez les paramètres :
   - URI de redirection valides : `http://localhost:8000/auth/facebook/callback`
   - Domaines d'application : `localhost`

### 3. Obtenir les clés d'API

1. Allez dans Paramètres > De base
2. Copiez l'ID de l'application et la Clé secrète de l'application

### 4. Mettre à jour le fichier .env

Remplacez les valeurs dans votre fichier `.env` :

```env
FACEBOOK_CLIENT_ID=votre_app_id_facebook
FACEBOOK_CLIENT_SECRET=votre_app_secret_facebook
FACEBOOK_REDIRECT_URI=http://localhost:8000/auth/facebook/callback
```

### 5. Tester la connexion

1. Démarrez votre serveur Laravel : `php artisan serve`
2. Allez sur la page de connexion : `http://localhost:8000/login`
3. Cliquez sur "Continuer avec Facebook"
4. Autorisez l'application et testez la connexion

## Fonctionnalités implémentées

- ✅ Connexion avec Facebook
- ✅ Création automatique de compte utilisateur
- ✅ Redirection vers le dashboard après connexion
- ✅ Gestion des erreurs de connexion
- ✅ Interface utilisateur avec bouton Facebook

## Structure des fichiers modifiés

- `app/Http/Controllers/FacebookAuthController.php` - Contrôleur d'authentification
- `config/services.php` - Configuration des services
- `routes/web.php` - Routes d'authentification Facebook
- `resources/views/auth/login.blade.php` - Interface de connexion
- `app/Models/User.php` - Modèle utilisateur mis à jour
- Migration pour ajouter `facebook_id` à la table users

## Notes importantes

- Les utilisateurs connectés via Facebook auront un mot de passe généré automatiquement
- L'ID Facebook est stocké pour éviter les doublons
- La redirection se fait vers `/dashboard` après connexion réussie