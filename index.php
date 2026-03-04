<?php
require_once 'includes/init.php';
requireLogin();

$pageTitle = 'Mes Paiements';

// on récupère les paiements du user connecté
$stmt = $pdo->prepare("SELECT * FROM payments WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$payments = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<h2>Mes paiements</h2>

<?php if (empty($payments)): ?>
    <div class="empty-state">
        <p>Aucun paiement pour le moment</p>
        <a href="payment.php" class="btn">Effectuer un paiement</a>
    </div>
<?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Réf.</th>
                    <th>Montant</th>
                    <th>Carte</th>
                    <th>Exp.</th>
                    <th>Date</th>
                    <th>Message</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $p): ?>
                    <tr>
                        <td>#<?php echo $p['id']; ?></td>
                        <td><strong><?php echo number_format($p['amount'], 2, ',', ' '); ?> €</strong></td>
                        <td class="text-muted"><?php echo getCardLast4(decrypt($p['card_number_encrypted'])); ?></td>
                        <td><?php echo htmlspecialchars($p['card_expiry']); ?></td>
                        <td><?php echo date('d/m/Y à H:i', strtotime($p['created_at'])); ?></td>
                        <td class="message-box"><?php echo $p['message'] ? $p['message'] : '-'; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>