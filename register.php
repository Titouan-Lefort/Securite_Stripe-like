<?php
require_once 'includes/init.php';

$pageTitle = 'Inscription';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    if (empty($email) || empty($password) || empty($confirm)) {
        $error = "Tous les champs sont obligatoires";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email invalide";
    } elseif ($password !== $confirm) {
        $error = "Les mots de passe ne correspondent pas";
    } elseif (!isStrongPassword($password)) {
        $error = "Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial";
    } else {
        try {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
            $stmt->execute([$email, $hash]);
            $success = "Compte créé avec succès ! <a href='login.php'>Se connecter</a>";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "Cet email est déjà utilisé";
            } else {
                $error = "Erreur lors de la création du compte";
            }
        }
    }
}

require_once 'includes/header.php';
?>

<div class="auth-wrapper">
    <div class="card">
        <h2>Créer un compte</h2>

        <?php if ($error): ?>
            <?php echo showError($error); ?>
        <?php endif; ?>

        <?php if ($success): ?>
            <?php echo showSuccess($success); ?>
        <?php else: ?>
            <form method="POST">
                <div>
                    <label>Email</label>
                    <input type="email" name="email" placeholder="vous@exemple.com" required>
                </div>
                <div>
                    <label>Mot de passe</label>
                    <div class="input-password">
                        <input type="password" name="password" id="password" required>
                        <button type="button" class="toggle-pwd" onclick="togglePassword('password', this)">Afficher</button>
                    </div>
                    <p class="hint">Min. 8 caractères, 1 majuscule, 1 minuscule, 1 chiffre, 1 symbole</p>
                </div>
                <div>
                    <label>Confirmer le mot de passe</label>
                    <div class="input-password">
                        <input type="password" name="confirm" id="confirm" required>
                        <button type="button" class="toggle-pwd" onclick="togglePassword('confirm', this)">Afficher</button>
                    </div>
                </div>
                <div>
                    <button type="submit">S'inscrire</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
    <p class="auth-footer"><a href="login.php">Déjà inscrit ? Se connecter</a></p>
</div>

<script>
    function togglePassword(id, btn) {
        var input = document.getElementById(id);
        if (input.type === 'password') {
            input.type = 'text';
            btn.textContent = 'Masquer';
        } else {
            input.type = 'password';
            btn.textContent = 'Afficher';
        }
    }
</script>

<?php require_once 'includes/footer.php'; ?>