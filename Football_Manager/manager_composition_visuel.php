<?php
include_once 'config.php';
verifier_acces('manager');

$selected_match_id = $_GET['id_match'] ?? null;

$stmt_matchs = $pdo->query("SELECT id_match, adversaire, date_match FROM matchs ORDER BY date_match DESC");
$liste_matchs = $stmt_matchs->fetchAll();

$joueurs_effectif = [];
$composition_actuelle = [];
if ($selected_match_id) {
    $joueurs_effectif = $pdo->query("SELECT * FROM joueurs ORDER BY poste, nom")->fetchAll();
    $stmt_compo = $pdo->prepare("SELECT id_joueur, position_terrain FROM compositions WHERE id_match = ?");
    $stmt_compo->execute([$selected_match_id]);
    $composition_actuelle_raw = $stmt_compo->fetchAll(PDO::FETCH_KEY_PAIR); // Clé = id_joueur, Valeur = position
    $composition_actuelle = $composition_actuelle_raw;
}

$pageTitle = "Composition Visuelle";
include 'header.php';
?>

    <link rel="stylesheet" href="composition.css">

    <h1>Éditeur de Composition Tactique</h1>

    <form action="manager_composition_visuel.php" method="get" id="match-select-form">
        <div class="form-group">
            <label for="id_match">Choisissez un match :</label>
            <select name="id_match" id="id_match" onchange="this.form.submit()">
                <option value="">-- Sélectionner un match --</option>
                <?php foreach ($liste_matchs as $match): ?>
                    <option value="<?php echo $match['id_match']; ?>" <?php echo ($selected_match_id == $match['id_match']) ? 'selected' : ''; ?>>
                        <?php echo date('d/m/Y', strtotime($match['date_match'])) . ' vs ' . htmlspecialchars($match['adversaire']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>

<?php if ($selected_match_id):?>
    <div class="composition-container">
        <div id="players-list-container">
            <h3>Effectif disponible</h3>
            <div id="available-players" class="player-pool">
                <?php
                foreach ($joueurs_effectif as $joueur) {
                    if (!array_key_exists($joueur['id_joueur'], $composition_actuelle)) {
                        echo '<div class="player-token" draggable="true" id="player-' . $joueur['id_joueur'] . '">' .
                            '<span class="player-name">' . htmlspecialchars($joueur['prenom']) . ' ' . htmlspecialchars($joueur['nom']) . '</span>' .
                            '<span class="player-number">' . $joueur['numero_maillot'] . '</span>' .
                            '</div>';
                    }
                }
                ?>
            </div>
            <h3>Banc des remplaçants</h3>
            <div id="bench" class="player-pool drop-zone">
                <?php
                foreach ($joueurs_effectif as $joueur) {
                    if (isset($composition_actuelle[$joueur['id_joueur']]) && $composition_actuelle[$joueur['id_joueur']] === 'Remplaçant') {
                        echo '<div class="player-token" draggable="true" id="player-' . $joueur['id_joueur'] . '" data-position="Remplaçant">' .
                            '<span class="player-name">' . htmlspecialchars($joueur['prenom']) . ' ' . htmlspecialchars($joueur['nom']) . '</span>' .
                            '<span class="player-number">' . $joueur['numero_maillot'] . '</span>' .
                            '</div>';
                    }
                }
                ?>
            </div>
        </div>

        <div id="pitch-container">
            <div id="pitch">
                <?php
                foreach ($joueurs_effectif as $joueur) {
                    if (isset($composition_actuelle[$joueur['id_joueur']]) && $composition_actuelle[$joueur['id_joueur']] !== 'Remplaçant') {
                        echo '<div class="player-token on-pitch" draggable="true" id="player-' . $joueur['id_joueur'] . '" data-position="' . $composition_actuelle[$joueur['id_joueur']] . '" style="/* Style initial positionné par JS au chargement */">' .
                            '<span class="player-name">' . htmlspecialchars($joueur['prenom']) . ' ' . htmlspecialchars($joueur['nom']) . '</span>' .
                            '<span class="player-number">' . $joueur['numero_maillot'] . '</span>' .
                            '</div>';
                    }
                }
                ?>
            </div>
        </div>
    </div>

    <form action="manager_compositions.php" method="post" id="save-composition-form" style="margin-top:20px;">
        <input type="hidden" name="id_match" value="<?php echo $selected_match_id; ?>">
        <button type="submit" class="btn btn-primary">Enregistrer la Composition</button>
    </form>

<?php endif; ?>

    <script src="composition.js"></script>

<?php include 'footer.php'; ?>