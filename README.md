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

Le serveur MySQL doit tourner sur `localhost` avec l'utilisateur `root` et **pas de mot de passe**.

````

### 4. Lancer le serveur PHP

Depuis la racine du projet :

```bash
php -S localhost:8000
````

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

Pour créer un compte utilisateur, aller sur :

```
http://localhost:8000/register.php
```
