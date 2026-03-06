# SecurePay — Système de paiement sécurisé

Projet PHP de simulation d'un système de paiement type Stripe, avec authentification, chiffrement des cartes bancaires, historique des paiements, panneau d'administration et remboursements.

## Prérequis

- **PHP 7.4+** (ou supérieur)
- **MySQL / MariaDB** (avec un accès root sans mot de passe par défaut)
- Un navigateur web

## Installation

### 1. Cloner le projet

```bash
git clone https://github.com/<votre-utilisateur>/Securite_Stripe-like.git
cd Securite_Stripe-like
```

### 2. Vérifier que PHP est installé

```bash
php -v
```

### 3. Vérifier que MySQL/MariaDB est lancé

Le serveur MySQL doit tourner sur `localhost` avec l'utilisateur `root` et **pas de mot de passe**. Si votre configuration est différente, modifiez le fichier `includes/config.php` :

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'paiement');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### 4. Lancer le serveur PHP

Depuis la racine du projet :

```bash
php -S localhost:8000
```

### 5. Installer la base de données

Ouvrir dans le navigateur :

```
http://localhost:8000/install.php
```

Ce script crée automatiquement :
- La base de données `paiement`
- Les tables `users`, `payments`, `refunds`
- Un compte administrateur par défaut

### 6. Se connecter

Aller sur :

```
http://localhost:8000/login.php
```

**Compte admin par défaut :**
- Email : `admin@admin.com`
- Mot de passe : `Admin123!`

Pour créer un compte utilisateur, aller sur :

```
http://localhost:8000/register.php
```

## Structure du projet

```
├── includes/
│   ├── config.php        # Configuration BDD + clé de chiffrement
│   ├── Database.php       # Connexion PDO (singleton)
│   ├── functions.php      # Fonctions utilitaires (chiffrement, CSRF, etc.)
│   ├── header.php         # En-tête HTML commun
│   ├── footer.php         # Pied de page commun
│   └── init.php           # Initialisation (session, headers, chargement)
├── public/
│   └── css/
│       └── style.css      # Feuille de styles
├── admin.php              # Panneau d'administration (remboursements)
├── index.php              # Historique des paiements (utilisateur)
├── install.php            # Script d'installation de la BDD
├── login.php              # Page de connexion
├── logout.php             # Déconnexion
├── payment.php            # Formulaire de paiement
├── register.php           # Inscription
└── README.md
```

## Fonctionnalités

- **Authentification** : inscription, connexion, déconnexion avec mots de passe hashés (bcrypt)
- **Chiffrement des cartes** : numéros chiffrés en AES-256-CBC, seuls les 4 derniers chiffres sont affichés
- **Paiements** : formulaire avec éditeur de texte riche (gras, italique, couleur)
- **Administration** : vue de tous les paiements, système de remboursement partiel/total
- **Protection CSRF** : tokens sur tous les formulaires
- **Protection XSS** : échappement HTML, nettoyage des messages
- **Headers de sécurité** : X-Content-Type-Options, X-Frame-Options, X-XSS-Protection
