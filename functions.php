<?php
// Fonctions utilitaires

// Vérifier si le mot de passe est fort
function isStrongPassword($password)
{
    // Au moins 8 caractères, 1 majuscule, 1 minuscule, 1 chiffre, 1 caractère spécial
    if (strlen($password) < 8) return false;
    if (!preg_match('/[A-Z]/', $password)) return false;
    if (!preg_match('/[a-z]/', $password)) return false;
    if (!preg_match('/[0-9]/', $password)) return false;
    if (!preg_match('/[^A-Za-z0-9]/', $password)) return false;
    return true;
}

// Chiffrer les données sensibles
function encrypt($data)
{
    $key = hash('sha256', ENCRYPTION_KEY);
    $iv = openssl_random_pseudo_bytes(16);
    $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
    return base64_encode($encrypted . '::' . $iv);
}

// Déchiffrer les données
function decrypt($data)
{
    $key = hash('sha256', ENCRYPTION_KEY);
    list($encrypted, $iv) = explode('::', base64_decode($data), 2);
    return openssl_decrypt($encrypted, 'AES-256-CBC', $key, 0, $iv);
}

// Obtenir les 4 premiers et 4 derniers chiffres d'une carte
function getCardDisplay($cardNumber)
{
    $first4 = substr($cardNumber, 0, 4);
    $last4 = substr($cardNumber, -4);
    return $first4 . ' **** **** ' . $last4;
}

// Obtenir seulement les 4 derniers chiffres
function getCardLast4($cardNumber)
{
    return '**** **** **** ' . substr($cardNumber, -4);
}

// Vérifier si l'utilisateur est connecté
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

// Vérifier si l'utilisateur est admin
function isAdmin()
{
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == true;
}

// Rediriger si non connecté
function requireLogin()
{
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

// Rediriger si non admin
function requireAdmin()
{
    requireLogin();
    if (!isAdmin()) {
        header('Location: index.php');
        exit;
    }
}

// Nettoyer le HTML mais garder le formatage
function sanitizeMessage($message)
{
    // Autoriser uniquement certaines balises HTML pour le formatage
    return strip_tags($message, '<b><i><strong><em><span>');
}
