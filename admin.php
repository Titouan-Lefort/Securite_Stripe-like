<?php
require_once 'includes/init.php';
requireAdmin();

$pageTitle = 'Administration';

// tous les paiements avec email user + montant déjà remboursé
$stmt = $pdo->query("
    SELECT p.*, u.email, 
    (SELECT COALESCE(SUM(r.amount), 0) FROM refunds r WHERE r.payment_id = p.id) as refunded_amount
    FROM payments p 
    JOIN users u ON p.user_id = u.id 
    ORDER BY p.created_at DESC
");
$payments = $stmt->fetchAll();

// traitement remboursement
$refundError = '';
$refundSuccess = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['refund'])) {
    // verification token CSRF
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $refundError = "Erreur de sécurité, veuillez réessayer";
    } else {
        $paymentId = $_POST['payment_id'];
        $refundAmount = $_POST['refund_amount'];

        // on vérifie combien il reste à rembourser
        $stmt = $pdo->prepare("SELECT amount FROM payments WHERE id = ?");
        $stmt->execute([$paymentId]);
        $payment = $stmt->fetch();

        $stmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM refunds WHERE payment_id = ?");
        $stmt->execute([$paymentId]);
        $refunded = $stmt->fetch()['total'];

        $remaining = $payment['amount'] - $refunded;

        if ($refundAmount <= 0) {
            $refundError = "Le montant doit être positif";
        } elseif ($refundAmount > $remaining) {
            $refundError = "Le montant du remboursement dépasse le montant restant ($remaining €)";
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO refunds (payment_id, amount) VALUES (?, ?)");
                $stmt->execute([$paymentId, $refundAmount]);
                $refundSuccess = "Remboursement de $refundAmount € effectué";

                // on recharge la liste
                $stmt = $pdo->query("
                SELECT p.*, u.email, 
                (SELECT COALESCE(SUM(r.amount), 0) FROM refunds r WHERE r.payment_id = p.id) as refunded_amount
                FROM payments p 
                JOIN users u ON p.user_id = u.id 
                ORDER BY p.created_at DESC
            ");
                $payments = $stmt->fetchAll();
            } catch (PDOException $e) {
                $refundError = "Erreur lors du remboursement";
            }
        }
    }
}

require_once 'includes/header.php';
?>

<h2>Administration</h2>

<?php if ($refundError): ?>
    <?php echo showError($refundError); ?>
<?php endif; ?>
<?php if ($refundSuccess): ?>
    <?php echo showSuccess($refundSuccess); ?>
<?php endif; ?>

<?php if (empty($payments)): ?>
    <div class="empty-state">
        <p>Aucun paiement enregistré</p>
    </div>
<?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Réf.</th>
                    <th>Utilisateur</th>
                    <th>Montant</th>
                    <th>Remboursé</th>
                    <th>Restant</th>
                    <th>Exp.</th>
                    <th>Date</th>
                    <th>Message</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $p): ?>
                    <?php $restant = $p['amount'] - $p['refunded_amount']; ?>
                    <tr>
                        <td>#<?php echo $p['id']; ?></td>
                        <td><?php echo htmlspecialchars($p['email']); ?></td>
                        <td><strong><?php echo number_format($p['amount'], 2, ',', ' '); ?> €</strong></td>
                        <td><?php echo number_format($p['refunded_amount'], 2, ',', ' '); ?> €</td>
                        <td><?php echo number_format($restant, 2, ',', ' '); ?> €</td>
                        <td><?php echo htmlspecialchars($p['card_expiry']); ?></td>
                        <td><?php echo date('d/m/Y à H:i', strtotime($p['created_at'])); ?></td>
                        <td class="message-box"><?php echo $p['message'] ? $p['message'] : '-'; ?></td>
                        <td>
                            <?php if ($restant > 0): ?>
                                <button class="btn-sm btn-outline" onclick="toggleRefund(<?php echo $p['id']; ?>)">Rembourser</button>
                                <div id="refund-<?php echo $p['id']; ?>" class="refund-form" style="display:none;margin-top:.5rem">
                                    <form method="POST" style="display:flex;gap:.4rem;align-items:center">
                                        <?php echo csrfInput(); ?>
                                        <input type="hidden" name="payment_id" value="<?php echo $p['id']; ?>">
                                        <input type="number" name="refund_amount" step="0.01" max="<?php echo $restant; ?>" placeholder="€" required style="width:80px">
                                        <button type="submit" name="refund" class="btn-sm btn-danger">OK</button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <span class="badge badge-ok">Remboursé</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<script>
    function toggleRefund(id) {
        var el = document.getElementById('refund-' + id);
        el.style.display = (el.style.display === 'none') ? 'block' : 'none';
    }
</script>

<?php require_once 'includes/footer.php'; ?>