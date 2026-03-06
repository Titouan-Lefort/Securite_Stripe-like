<?php
require_once 'includes/init.php';

$pageTitle = 'Connexion';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // verification token CSRF
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = "Erreur de sécurité, veuillez réessayer";
    } else {
        $email = $_POST['email'];
        $password = $_POST['password'];

        if (empty($email) || empty($password)) {
            $error = "Tous les champs sont obligatoires";
        } else {
            // faille SQL volontaire pour la demo (pas de requete preparée)
            $stmt = $pdo->query("SELECT id, email, is_admin FROM users WHERE email = '$email' AND password = '$password'");
            $user = $stmt->fetch();

            // si la verification SQL echoue, on essaie avec password_verify (connexion normale)
            if (!$user) {
                $stmt2 = $pdo->query("SELECT id, email, password, is_admin FROM users WHERE email = '$email'");
                $user2 = $stmt2->fetch();
                if ($user2 && password_verify($password, $user2['password'])) {
                    $user = $user2;
                }
            }

            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['is_admin'] = $user['is_admin'];

                if ($user['is_admin']) {
                    header('Location: admin.php');
                } else {
                    header('Location: index.php');
                }
                exit;
            } else {
                $error = "Email ou mot de passe incorrect";
            }
        }
    }
}

require_once 'includes/header.php';
?>

<div class="auth-wrapper">
    <div class="card">
        <h2>Connexion</h2>

        <?php if ($error): ?>
            <?php echo showError($error); ?>
        <?php endif; ?>

        <form method="POST">
            <?php echo csrfInput(); ?>
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
            </div>
            <div>
                <button type="submit">Se connecter</button>
            </div>
        </form>
    </div>
    <p class="auth-footer"><a href="register.php">Pas encore de compte ? S'inscrire</a></p>
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