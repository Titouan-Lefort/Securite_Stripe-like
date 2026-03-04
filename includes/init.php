<?php
// fichier d'initialisation - on charge tout ici
session_start();

require_once __DIR__ . '/config.php';
require_once INCLUDES_PATH . '/Database.php';
require_once INCLUDES_PATH . '/functions.php';

// connexion bdd
$db = Database::getInstance();
$pdo = $db->getConnection();
