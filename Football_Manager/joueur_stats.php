<?php
include_once 'config.php';
verifier_acces('joueur');

$id_utilisateur = $_SESSION['user_id'];

$stmt = $pdo->prepare(
    "SELECT j.*
     FROM joueurs j
     JOIN utilisateurs u ON j.id_joueur = u.id_joueur_associe
     WHERE u.id_utilisateur = ?"
);
$stmt->execute([$id_utilisateur]);
$joueur = $stmt->fetch();

$pageTitle = "Mes Informations";
include 'header.php';
?>

<h1>Mes Informations Personnelles</h1>

<?php if ($joueur): ?>
    <table>
        <tr>
            <th>Nom</th>
            <td><?php echo htmlspecialchars($joueur['nom']); ?></td>
        </tr>
        <tr>
            <th>Prénom</th>
            <td><?php echo htmlspecialchars($joueur['prenom']); ?></td>
        </tr>
        <tr>
            <th>Poste</th>
            <td><?php echo htmlspecialchars($joueur['poste']); ?></td>
        </tr>
        <tr>
            <th>Numéro de Maillot</th>
            <td><?php echo htmlspecialchars($joueur['numero_maillot']); ?></td>
        </tr>
    </table>
<?php else: ?>
    <div class="alert alert-danger">
        Aucune information de joueur n'est associée à votre compte. Veuillez contacter le manager.
    </div>
<?php endif; ?>

<?php include 'footer.php'; ?>
