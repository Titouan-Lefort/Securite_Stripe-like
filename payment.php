<?php
require_once 'includes/init.php';
requireLogin();

$pageTitle = 'Faire un paiement';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // verification token CSRF
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = "Erreur de sécurité, veuillez réessayer";
    } else {
        $amount = $_POST['amount'];
        $cardNumber = str_replace(' ', '', $_POST['card_number']);
        $cardExpiry = $_POST['card_expiry'];
        $cvv = $_POST['cvv'];
        $message = $_POST['message'];

        // verifications
        if (empty($amount) || empty($cardNumber) || empty($cardExpiry) || empty($cvv)) {
            $error = "Tous les champs sont obligatoires";
        } elseif ($amount <= 0) {
            $error = "Le montant doit être positif";
        } elseif (strlen($cardNumber) > 16 || !ctype_digit($cardNumber)) {
            $error = "Numéro de carte invalide (16 chiffres maximum)";
        } elseif (!preg_match('/^\d{2}\/\d{2}$/', $cardExpiry)) {
            $error = "Date d'expiration invalide (MM/YY)";
        } elseif (!preg_match('/^\d{3}$/', $cvv)) {
            $error = "CVV invalide (3 chiffres)";
        } else {
            try {
                // on chiffre le numero de carte, le cvv n'est jamais stocké
                $encryptedCard = encrypt($cardNumber);
                $cleanMessage = sanitizeMessage($message);

                $stmt = $pdo->prepare("INSERT INTO payments (user_id, amount, card_number_encrypted, card_expiry, message) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$_SESSION['user_id'], $amount, $encryptedCard, $cardExpiry, $cleanMessage]);

                $success = "Paiement effectué avec succès ! <a href='index.php'>Voir mes paiements</a>";
            } catch (PDOException $e) {
                $error = "Erreur lors du paiement : " . $e->getMessage();
            }
        }
    }
}

require_once 'includes/header.php';
?>

<h2>Nouveau paiement</h2>

<?php if ($error): ?>
    <?php echo showError($error); ?>
<?php endif; ?>

<?php if ($success): ?>
    <?php echo showSuccess($success); ?>
<?php else: ?>
    <div class="card">
        <form method="POST">
            <?php echo csrfInput(); ?>
            <div>
                <label>Montant (€)</label>
                <input type="number" name="amount" step="0.01" min="0.01" placeholder="0,00" required>
            </div>

            <div class="form-row">
                <div style="flex:2">
                    <label>Numéro de carte</label>
                    <input type="text" name="card_number" id="card_number" maxlength="19" placeholder="1234 5678 9012 3456" autocomplete="cc-number" required>
                </div>
                <div style="flex:1">
                    <label>Exp.</label>
                    <input type="text" name="card_expiry" id="card_expiry" maxlength="5" placeholder="MM/YY" autocomplete="cc-exp" required>
                </div>
                <div style="flex:1">
                    <label>CVV</label>
                    <input type="text" name="cvv" maxlength="3" placeholder="123" autocomplete="cc-csc" required>
                </div>
            </div>

            <div>
                <label>Message (optionnel)</label>
                <div class="editor-toolbar">
                    <button type="button" onclick="formatText('bold')"><b>B</b></button>
                    <button type="button" onclick="formatText('italic')"><i>I</i></button>
                    <button type="button" onclick="changeColor('red')" style="color:#e74c3c">A</button>
                    <button type="button" onclick="changeColor('blue')" style="color:#3498db">A</button>
                    <button type="button" onclick="changeColor('green')" style="color:#27ae60">A</button>
                </div>
                <div id="editor" contenteditable="true"></div>
                <input type="hidden" name="message" id="message">
            </div>

            <div>
                <button type="submit" onclick="document.getElementById('message').value = document.getElementById('editor').innerHTML;">Payer</button>
            </div>
        </form>
    </div>
<?php endif; ?>

<script>
    // editeur de texte riche (gras, italique, couleur)
    function formatText(tag) {
        document.execCommand(tag, false, null);
    }

    function changeColor(color) {
        document.execCommand('foreColor', false, color);
    }

    // formatage auto du numero de carte (espace tous les 4 chiffres)
    document.getElementById('card_number').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\s/g, '');
        value = value.replace(/\D/g, ''); // que des chiffres

        if (value.length > 16) {
            value = value.substring(0, 16);
        }

        // on ajoute un espace tous les 4 chiffres
        let formatted = '';
        for (let i = 0; i < value.length; i++) {
            if (i > 0 && i % 4 === 0) formatted += ' ';
            formatted += value[i];
        }
        e.target.value = formatted;
    });
</script>

<?php require_once 'includes/footer.php'; ?>