<?php
include_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_login = $_SESSION['user_login'];
$user_role = $_SESSION['user_role'];

$pageTitle = "Tableau de Bord";
include 'header.php';
?>

<h1>Bienvenue, <?php echo htmlspecialchars($user_login); ?> !</h1>
<p>Vous êtes connecté en tant que <strong><?php echo $user_role; ?></strong>.</p>

<div style="margin-top: 40px;">
    <h2>Accès rapides</h2>
    <p>Utilisez la barre de navigation en haut de la page pour vous déplacer dans l'application.</p>

    <?php if ($user_role === 'manager'): ?>
        <h3>En tant que Manager, vous pouvez :</h3>
        <ul>
            <li><a href="manager_joueurs.php">Gérer les joueurs</a> de votre effectif.</li>
            <li><a href="manager_matchs.php">Planifier les prochains matchs</a> et voir les anciens.</li>
            <li><a href="manager_compositions.php">Créer les compositions d'équipe</a> pour chaque match.</li>
        </ul>
    <?php else: ?>
        <h3>En tant que Joueur, vous pouvez :</h3>
        <ul>
            <li><a href="joueur_matchs.php">Consulter les prochains matchs</a> et les compositions.</li>
            <li><a href="joueur_stats.php">Voir vos informations personnelles</a>.</li>
        </ul>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
