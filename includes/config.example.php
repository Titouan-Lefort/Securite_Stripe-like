<?php
// Exemple de fichier de configuration
// Copiez ce fichier vers includes/config.php et modifiez les valeurs

// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'paiement');
define('DB_USER', 'root');
define('DB_PASS', '');

// Clé de chiffrement (IMPORTANT : Générez une clé aléatoire unique)
// Vous pouvez utiliser : openssl_rand_pseudo_bytes(32)
define('ENCRYPTION_KEY', 'CHANGEZ_CETTE_CLE_PAR_UNE_CLE_ALEATOIRE_DE_32_CARACTERES_MINIMUM');

// Chemins
define('ROOT_PATH', dirname(__DIR__));
define('INCLUDES_PATH', ROOT_PATH . '/includes');
