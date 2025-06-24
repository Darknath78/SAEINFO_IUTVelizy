<?php
include_once 'config.php';
verifier_acces('manager');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_match'])) {
    $id_match = $_POST['id_match'];

    $pdo->beginTransaction();
    try {
        $stmt_delete = $pdo->prepare("DELETE FROM compositions WHERE id_match = ?");
        $stmt_delete->execute([$id_match]);

        if (isset($_POST['joueurs'])) {
            $stmt_insert = $pdo->prepare("INSERT INTO compositions (id_match, id_joueur, position_terrain) VALUES (?, ?, ?)");
            foreach ($_POST['joueurs'] as $player_data) {
                if (isset($player_data['id']) && isset($player_data['position'])) {
                    $stmt_insert->execute([$id_match, $player_data['id'], $player_data['position']]);
                }
            }
        }

        $pdo->commit();

        header("Location: manager_composition_visuel.php?id_match=" . $id_match . "&success=1");
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        header("Location: manager_composition_visuel.php?id_match=" . $id_match . "&error=1");
        exit();
    }
} else {
    header("Location: manager_composition_visuel.php");
    exit();
}
?>
