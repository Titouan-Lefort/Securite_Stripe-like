<?php
// Script de migration pour retirer la colonne CVV de la base de données

require_once 'includes/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Vérifier si la colonne existe
    $stmt = $pdo->query("SHOW COLUMNS FROM payments LIKE 'cvv_encrypted'");

    if ($stmt->rowCount() > 0) {
        // Supprimer la colonne CVV
        $pdo->exec("ALTER TABLE payments DROP COLUMN cvv_encrypted");
        echo "✅ Colonne CVV supprimée avec succès pour des raisons de sécurité.\n";
    } else {
        echo "ℹ️ La colonne CVV n'existe pas ou a déjà été supprimée.\n";
    }

    echo "\n✅ Migration terminée !\n";
} catch (PDOException $e) {
    die("❌ Erreur : " . $e->getMessage() . "\n");
}
