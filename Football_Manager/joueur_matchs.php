<?php
include_once 'config.php';
verifier_acces('joueur');

$stmt = $pdo->query("SELECT * FROM matchs ORDER BY date_match DESC");
$matchs = $stmt->fetchAll();

$pageTitle = "Calendrier des Matchs";
include 'header.php';
?>

    <h1>Matchs et Compositions</h1>
    <p>Consultez ici le calendrier des matchs et les compositions d'équipe lorsqu'elles sont disponibles.</p>

<?php foreach ($matchs as $match): ?>
    <div style="border: 1px solid #ccc; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
        <h3>
            <?php echo htmlspecialchars($match['tournoi']); ?>:
            Notre Équipe vs <?php echo htmlspecialchars($match['adversaire']); ?>
        </h3>
        <p>
            <strong>Date :</strong> <?php echo date('d/m/Y à H:i', strtotime($match['date_match'])); ?><br>
            <strong>Lieu :</strong> <?php echo htmlspecialchars($match['lieu']); ?>
        </p>
        <?php if ($match['score_equipe'] !== null): ?>
            <p><strong>Score Final :</strong> <?php echo htmlspecialchars($match['score_equipe']); ?> - <?php echo htmlspecialchars($match['score_adversaire']); ?></p>
        <?php endif; ?>

        <h4>Composition de l'équipe :</h4>
        <?php
        $stmt_compo = $pdo->prepare(
            "SELECT j.nom, j.prenom, c.position_terrain
             FROM compositions c
             JOIN joueurs j ON c.id_joueur = j.id_joueur
             WHERE c.id_match = ? ORDER BY j.nom"
        );
        $stmt_compo->execute([$match['id_match']]);
        $composition = $stmt_compo->fetchAll();

        if ($composition):
            ?>
            <ul>
                <?php foreach ($composition as $joueur_compo): ?>
                    <li>
                        <?php
                        $position_parts = explode('|', $joueur_compo['position_terrain']);
                        $position_name = htmlspecialchars($position_parts[0]);
                        ?>
                        <strong><?php echo $position_name; ?> :</strong>
                        <?php echo htmlspecialchars($joueur_compo['prenom'] . ' ' . $joueur_compo['nom']); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>La composition pour ce match n'a pas encore été définie.</p>
        <?php endif; ?>
    </div>
<?php endforeach; ?>

<?php include 'footer.php'; ?>