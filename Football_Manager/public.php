<?php
include_once 'config.php';

$stmt = $pdo->query(
    "SELECT * FROM matchs
     WHERE date_match < NOW() AND score_equipe IS NOT NULL
     ORDER BY date_match DESC
     LIMIT 10"
);
$matchs_joues = $stmt->fetchAll();

$pageTitle = "Résultats Publics";
include 'header.php';
?>

<h1>Derniers Résultats</h1>
<p>Voici les résultats des derniers matchs de notre équipe.</p>

<table>
    <thead>
    <tr>
        <th>Date</th>
        <th>Tournoi</th>
        <th>Match</th>
        <th>Score Final</th>
    </tr>
    </thead>
    <tbody>
    <?php if ($matchs_joues): ?>
        <?php foreach ($matchs_joues as $match): ?>
            <tr>
                <td><?php echo date('d/m/Y', strtotime($match['date_match'])); ?></td>
                <td><?php echo htmlspecialchars($match['tournoi']); ?></td>
                <td>Notre Équipe vs <?php echo htmlspecialchars($match['adversaire']); ?></td>
                <td>
                    <strong><?php echo htmlspecialchars($match['score_equipe']); ?> - <?php echo htmlspecialchars($match['score_adversaire']); ?></strong>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="4" style="text-align:center;">Aucun résultat disponible pour le moment.</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>
