<?php
include_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_login = $_SESSION['user_login'];
$user_role = $_SESSION['user_role'];


$stmt_joueurs = $pdo->query("SELECT COUNT(*) FROM joueurs");
$total_joueurs = $stmt_joueurs->fetchColumn();

$stmt_matchs = $pdo->query("SELECT COUNT(*) FROM matchs");
$total_matchs = $stmt_matchs->fetchColumn();

$prochain_match = null;
$stmt_prochain_match = $pdo->query("SELECT adversaire, date_match FROM matchs WHERE date_match > NOW() ORDER BY date_match ASC LIMIT 1");
if ($stmt_prochain_match->rowCount() > 0) {
    $prochain_match = $stmt_prochain_match->fetch();
}

$pageTitle = "Tableau de Bord";
include 'header.php';
?>

<h1>Bienvenue, <?php echo htmlspecialchars($user_login); ?> !</h1>
<p>Vous êtes connecté en tant que <strong><?php echo $user_role; ?></strong>.</p>

<div style="display: flex; gap: 20px; margin-top: 40px; text-align: center;">
    <div style="flex: 1; padding: 20px; background-color: #e3f2fd; border-radius: 8px;">
        <h3>Effectif Total</h3>
        <p style="font-size: 2.5rem; font-weight: bold; color: var(--primary-color); margin: 0;"><?php echo $total_joueurs; ?></p>
        <p>Joueurs</p>
    </div>
    <div style="flex: 1; padding: 20px; background-color: #e8f5e9; border-radius: 8px;">
        <h3>Matchs Planifiés</h3>
        <p style="font-size: 2.5rem; font-weight: bold; color: var(--success-color); margin: 0;"><?php echo $total_matchs; ?></p>
        <p>Matchs au total</p>
    </div>
    <div style="flex: 1; padding: 20px; background-color: #fff3e0; border-radius: 8px;">
        <h3>Prochaine Rencontre</h3>
        <?php if ($prochain_match): ?>
            <p style="font-size: 1.5rem; font-weight: bold; color: var(--accent-color); margin: 0;">
                vs <?php echo htmlspecialchars($prochain_match['adversaire']); ?>
            </p>
            <p><?php echo date('d/m/Y à H:i', strtotime($prochain_match['date_match'])); ?></p>
        <?php else: ?>
            <p style="font-size: 1.5rem; font-weight: bold; color: var(--accent-color); margin: 0;">Aucun match à venir</p>
        <?php endif; ?>
    </div>
</div>

<div style="margin-top: 40px;">
    <h2>Accès rapides</h2>
    <p>Utilisez la barre de navigation en haut de la page pour vous déplacer dans l'application.</p>
</div>

<?php include 'footer.php'; ?>
