<?php
include_once 'config.php';
verifier_acces('manager');

$success_message = '';
$error_message = '';

if (isset($_GET['delete_id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM matchs WHERE id_match = ?");
        $stmt->execute([$_GET['delete_id']]);
        $success_message = "Le match a été supprimé avec succès.";
    } catch (PDOException $e) {
        $error_message = "Erreur lors de la suppression : " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_match = $_POST['id_match'] ?? null;
    $adversaire = $_POST['adversaire'];
    $date_match = $_POST['date_match'];
    $lieu = $_POST['lieu'];
    $tournoi = $_POST['tournoi'];

    $score_equipe = (isset($_POST['score_equipe']) && $_POST['score_equipe'] !== '') ? $_POST['score_equipe'] : null;
    $score_adversaire = (isset($_POST['score_adversaire']) && $_POST['score_adversaire'] !== '') ? $_POST['score_adversaire'] : null;

    try {
        if ($id_match) {
            $stmt = $pdo->prepare("UPDATE matchs SET adversaire = ?, date_match = ?, lieu = ?, tournoi = ?, score_equipe = ?, score_adversaire = ? WHERE id_match = ?");
            $stmt->execute([$adversaire, $date_match, $lieu, $tournoi, $score_equipe, $score_adversaire, $id_match]);
            $success_message = "Le match a été mis à jour avec succès.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO matchs (adversaire, date_match, lieu, tournoi, score_equipe, score_adversaire) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$adversaire, $date_match, $lieu, $tournoi, $score_equipe, $score_adversaire]);
            $success_message = "Le match a été ajouté avec succès.";
        }
    } catch (PDOException $e) {
        $error_message = "Erreur lors de l'opération : " . $e->getMessage();
    }
}

$match_a_modifier = null;
if (isset($_GET['edit_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM matchs WHERE id_match = ?");
    $stmt->execute([$_GET['edit_id']]);
    $match_a_modifier = $stmt->fetch();
    if ($match_a_modifier) {
        $match_a_modifier['date_match_form'] = date('Y-m-d\TH:i', strtotime($match_a_modifier['date_match']));
    }
}

$stmt = $pdo->query("SELECT * FROM matchs ORDER BY date_match DESC");
$matchs = $stmt->fetchAll();

$pageTitle = "Gérer les Matchs";
include 'header.php';
?>

    <h1>Gestion des Matchs</h1>

<?php if ($success_message): ?><div class="alert alert-success"><?php echo $success_message; ?></div><?php endif; ?>
<?php if ($error_message): ?><div class="alert alert-danger"><?php echo $error_message; ?></div><?php endif; ?>

    <h3 style="padding-bottom: 10px; border-bottom: 2px solid #eee; margin-bottom: 25px;"><?php echo $match_a_modifier ? 'Modifier le match' : 'Planifier un nouveau match'; ?></h3>
    <form action="manager_matchs.php" method="post">
        <input type="hidden" name="id_match" value="<?php echo $match_a_modifier['id_match'] ?? ''; ?>">
        <div class="form-group">
            <label for="adversaire">Adversaire</label>
            <input type="text" name="adversaire" id="adversaire" value="<?php echo htmlspecialchars($match_a_modifier['adversaire'] ?? ''); ?>" required>
        </div>
        <div class="form-group">
            <label for="date_match">Date et Heure</label>
            <input type="datetime-local" name="date_match" id="date_match" value="<?php echo $match_a_modifier['date_match_form'] ?? ''; ?>" required>
        </div>
        <div class="form-group">
            <label for="lieu">Lieu</label>
            <input type="text" name="lieu" id="lieu" value="<?php echo htmlspecialchars($match_a_modifier['lieu'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="tournoi">Tournoi</label>
            <input type="text" name="tournoi" id="tournoi" value="<?php echo htmlspecialchars($match_a_modifier['tournoi'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="score_equipe">Score Équipe</label>
            <input type="number" name="score_equipe" id="score_equipe" value="<?php echo htmlspecialchars($match_a_modifier['score_equipe'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="score_adversaire">Score Adversaire</label>
            <input type="number" name="score_adversaire" id="score_adversaire" value="<?php echo htmlspecialchars($match_a_modifier['score_adversaire'] ?? ''); ?>">
        </div>
        <button type="submit" class="btn btn-primary"><?php echo $match_a_modifier ? 'Mettre à jour' : 'Ajouter le match'; ?></button>
        <?php if ($match_a_modifier): ?>
            <a href="manager_matchs.php" class="btn btn-danger">Annuler</a>
        <?php endif; ?>
    </form>

    <h2 style="margin-top: 40px; padding-bottom: 10px; border-bottom: 2px solid #eee; margin-bottom: 25px;">Liste des Matchs</h2>
    <table>
        <thead>
        <tr>
            <th>Date</th>
            <th>Tournoi</th>
            <th>Match</th>
            <th>Score</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($matchs as $match): ?>
            <tr>
                <td><?php echo date('d/m/Y H:i', strtotime($match['date_match'])); ?></td>
                <td><?php echo htmlspecialchars($match['tournoi']); ?></td>
                <td>Notre Équipe vs <?php echo htmlspecialchars($match['adversaire']); ?></td>
                <td>
                    <?php
                    if ($match['score_equipe'] !== null) {
                        echo htmlspecialchars($match['score_equipe']) . ' - ' . htmlspecialchars($match['score_adversaire']);
                    } else {
                        echo 'À jouer';
                    }
                    ?>
                </td>
                <td class="actions">
                    <a href="manager_matchs.php?edit_id=<?php echo $match['id_match']; ?>" class="btn-success">Modifier</a>
                    <a href="manager_matchs.php?delete_id=<?php echo $match['id_match']; ?>" onclick="return confirm('Êtes-vous sûr ?');" class="btn-danger">Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

<?php include 'footer.php'; ?>