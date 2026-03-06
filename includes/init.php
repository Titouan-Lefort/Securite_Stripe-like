<?php
// fichier d'initialisation - on charge tout ici

// securité des cookies de session (httponly + samesite)
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Strict'
]);
session_start();

// headers de securité
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

require_once __DIR__ . '/config.php';
require_once INCLUDES_PATH . '/Database.php';
require_once INCLUDES_PATH . '/functions.php';

// connexion bdd
$db = Database::getInstance();
$pdo = $db->getConnection();
