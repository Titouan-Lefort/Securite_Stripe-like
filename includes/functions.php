<?php
// toutes les fonctions utiles du site

// verification mot de passe fort (8 car min, maj, min, chiffre, special)
function isStrongPassword($password)
{
    if (strlen($password) < 8) return false;
    if (!preg_match('/[A-Z]/', $password)) return false;
    if (!preg_match('/[a-z]/', $password)) return false;
    if (!preg_match('/[0-9]/', $password)) return false;
    if (!preg_match('/[^A-Za-z0-9]/', $password)) return false;
    return true;
}

// chiffrement AES-256-CBC
function encrypt($data)
{
    $key = hash('sha256', ENCRYPTION_KEY);
    $iv = openssl_random_pseudo_bytes(16);
    $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
    return base64_encode($encrypted . '::' . $iv);
}

function decrypt($data)
{
    $key = hash('sha256', ENCRYPTION_KEY);
    list($encrypted, $iv) = explode('::', base64_decode($data), 2);
    return openssl_decrypt($encrypted, 'AES-256-CBC', $key, 0, $iv);
}

// affichage carte bancaire (masquée)
function getCardDisplay($cardNumber)
{
    $first4 = substr($cardNumber, 0, 4);
    $last4 = substr($cardNumber, -4);
    return $first4 . ' **** **** ' . $last4;
}

function getCardLast4($cardNumber)
{
    return '**** **** **** ' . substr($cardNumber, -4);
}

// verifs session
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

function isAdmin()
{
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == true;
}

// redirections si pas connecté / pas admin
function requireLogin()
{
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function requireAdmin()
{
    requireLogin();
    if (!isAdmin()) {
        header('Location: index.php');
        exit;
    }
}

// nettoyer le html du message (on garde juste le gras, italique, couleurs)
function sanitizeMessage($message)
{
    return strip_tags($message, '<b><i><strong><em><span>');
}

// messages d'erreur / succès
function showError($msg)
{
    return '<div class="alert error">' . htmlspecialchars($msg) . '</div>';
}

function showSuccess($msg)
{
    return '<div class="alert success">' . $msg . '</div>';
}
