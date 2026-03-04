<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'SecurePay'; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="public/css/style.css">
</head>

<body>
    <header>
        <div class="container">
            <span class="logo">SecurePay</span>
            <?php if (isLoggedIn()): ?>
                <nav>
                    <?php if (isAdmin()): ?>
                        <a href="admin.php">Paiements</a>
                    <?php else: ?>
                        <a href="index.php">Historique</a>
                        <a href="payment.php">Payer</a>
                    <?php endif; ?>
                    <a href="logout.php">Déconnexion</a>
                    <span class="user-info"><?php echo htmlspecialchars($_SESSION['email']); ?></span>
                </nav>
            <?php endif; ?>
        </div>
    </header>
    <main class="container">