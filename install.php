<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="public/css/style.css">
</head>

<body>
    <header>
        <div class="container">
            <span class="logo">SecurePay</span>
        </div>
    </header>
    <main class="container">
        <h2>Installation de la base de données</h2>

        <?php
        // script d'install - crée la bdd et les tables

        $host = 'localhost';
        $user = 'root';
        $pass = '';

        try {
            $pdo = new PDO("mysql:host=$host", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // creation de la base
            $pdo->exec("CREATE DATABASE IF NOT EXISTS paiement CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE paiement");

            // table users
            $pdo->exec("CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(191) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                is_admin BOOLEAN DEFAULT FALSE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            // table paiements
            $pdo->exec("CREATE TABLE IF NOT EXISTS payments (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                amount DECIMAL(10,2) NOT NULL,
                card_number_encrypted TEXT NOT NULL,
                card_expiry VARCHAR(10) NOT NULL,
                message TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            // table remboursements
            $pdo->exec("CREATE TABLE IF NOT EXISTS refunds (
                id INT AUTO_INCREMENT PRIMARY KEY,
                payment_id INT NOT NULL,
                amount DECIMAL(10,2) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (payment_id) REFERENCES payments(id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            // compte admin par defaut
            $adminPassword = password_hash('Securepay@2026!', PASSWORD_DEFAULT);
            $pdo->exec("INSERT IGNORE INTO users (email, password, is_admin) VALUES ('admin@admin.com', '$adminPassword', TRUE)");

            echo '<div class="alert success">';
            echo 'Installation réussie !<br>';
            echo '<strong>Compte admin :</strong><br>';
            echo 'Email : admin@admin.com<br>';
            echo 'Mot de passe : Securepay@2026!<br><br>';
            echo '<a href="login.php" class="btn">Se connecter</a>';
            echo '</div>';
        } catch (PDOException $e) {
            echo '<div class="alert error">';
            echo 'Erreur : ' . $e->getMessage();
            echo '</div>';
        }
        ?>
    </main>
    <footer>
        <div class="container">
            <p>SecurePay &mdash; <?php echo date('Y'); ?></p>
        </div>
    </footer>
</body>

</html>