<?php
if (file_exists('config.php')) {
    include_once 'config.php';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? "Football Manager"; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav>
    <div class="logo">Football_Manager</div>
    <ul>
        <?php if (isset($_SESSION['user_id'])): ?>
            <li><a href="dashboard.php">Tableau de bord</a></li>
            <?php if ($_SESSION['user_role'] === 'manager'): ?>
                <li><a href="manager_joueurs.php">Gérer les Joueurs</a></li>
                <li><a href="manager_matchs.php">Gérer les Matchs</a></li>
                <li><a href="manager_composition_visuel.php">Compositions</a></li>
            <?php else: ?>
                <li><a href="joueur_matchs.php">Voir les Matchs</a></li>
                <li><a href="joueur_stats.php">Mes Infos</a></li>
            <?php endif; ?>
            <li><a href="logout.php">Déconnexion</a></li>
        <?php else: ?>
            <li><a href="public.php">Résultats Publics</a></li>
            <li><a href="index.php">Connexion</a></li>
        <?php endif; ?>
    </ul>
</nav>

<main class="container">
