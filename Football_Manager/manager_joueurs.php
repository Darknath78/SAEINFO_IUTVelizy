<?php
include_once 'config.php';
verifier_acces('manager');

$success_message = '';
$error_message = '';

if (isset($_GET['delete_id'])) {
    $id_to_delete = $_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM joueurs WHERE id_joueur = ?");
    $stmt->execute([$id_to_delete]);
    $success_message = "Le joueur a été supprimé avec succès.";
}

if (isset($_GET['delete_user_id'])) {
    $id_user_to_delete = $_GET['delete_user_id'];

    if ($id_user_to_delete == $_SESSION['user_id']) {
        $error_message = "Suppression non autorisée : vous ne pouvez pas supprimer votre propre compte.";
    } else {
        $stmt_check = $pdo->prepare("SELECT role FROM utilisateurs WHERE id_utilisateur = ?");
        $stmt_check->execute([$id_user_to_delete]);
        $user_to_delete = $stmt_check->fetch();

        $allow_delete = false;
        if ($user_to_delete) {
            if ($user_to_delete['role'] === 'joueur') {
                $allow_delete = true;
            }
            elseif ($user_to_delete['role'] === 'manager' && $_SESSION['user_login'] === 'manager') {
                $allow_delete = true;
            }
        }

        if ($allow_delete) {
            $stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE id_utilisateur = ?");
            $stmt->execute([$id_user_to_delete]);
            $success_message = "Le compte utilisateur a été supprimé avec succès.";
        } else {
            $error_message = "Suppression non autorisée : vous n'avez pas les droits suffisants pour supprimer ce compte.";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_joueur'])) {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $poste = $_POST['poste'];
    $numero_maillot = $_POST['numero_maillot'];
    $id_joueur = $_POST['id_joueur'] ?? null;

    if ($id_joueur) {
        $stmt = $pdo->prepare("UPDATE joueurs SET nom = ?, prenom = ?, poste = ?, numero_maillot = ? WHERE id_joueur = ?");
        $stmt->execute([$nom, $prenom, $poste, $numero_maillot, $id_joueur]);
        $success_message = "Le joueur a été modifié avec succès.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO joueurs (nom, prenom, poste, numero_maillot) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nom, $prenom, $poste, $numero_maillot]);
        $success_message = "Le joueur a été ajouté avec succès.";
    }
}

$joueur_a_modifier = null;
if (isset($_GET['edit_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM joueurs WHERE id_joueur = ?");
    $stmt->execute([$_GET['edit_id']]);
    $joueur_a_modifier = $stmt->fetch();
}

$stmt = $pdo->query(
    "SELECT j.*, u.id_utilisateur, u.login, u.role
     FROM joueurs j
     LEFT JOIN utilisateurs u ON j.id_joueur = u.id_joueur_associe
     ORDER BY j.nom, j.prenom"
);
$joueurs = $stmt->fetchAll();

$pageTitle = "Gérer les Joueurs";
include 'header.php';
?>

    <h1>Gestion des Joueurs</h1>

<?php if (!empty($_GET['success'])): ?>
    <div class="alert alert-success">L'opération a été réalisée avec succès !</div>
<?php endif; ?>
<?php if ($success_message): ?>
    <div class="alert alert-success"><?php echo $success_message; ?></div>
<?php endif; ?>
<?php if ($error_message): ?>
    <div class="alert alert-danger"><?php echo $error_message; ?></div>
<?php endif; ?>


    <div class="form-section">
        <h3 style="padding-bottom: 10px; border-bottom: 2px solid #eee; margin-bottom: 25px;"><?php echo $joueur_a_modifier ? 'Modifier le joueur' : 'Ajouter un nouveau joueur'; ?></h3>
        <form action="manager_joueurs.php" method="post">
            <input type="hidden" name="form_joueur" value="1">
            <input type="hidden" name="id_joueur" value="<?php echo $joueur_a_modifier['id_joueur'] ?? ''; ?>">
            <div class="form-group">
                <label for="nom">Nom</label>
                <input type="text" name="nom" id="nom" value="<?php echo htmlspecialchars($joueur_a_modifier['nom'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="prenom">Prénom</label>
                <input type="text" name="prenom" id="prenom" value="<?php echo htmlspecialchars($joueur_a_modifier['prenom'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="poste">Poste</label>
                <select name="poste" id="poste" required>
                    <option value="Gardien" <?php echo ($joueur_a_modifier['poste'] ?? '') === 'Gardien' ? 'selected' : ''; ?>>Gardien</option>
                    <option value="Défenseur" <?php echo ($joueur_a_modifier['poste'] ?? '') === 'Défenseur' ? 'selected' : ''; ?>>Défenseur</option>
                    <option value="Milieu" <?php echo ($joueur_a_modifier['poste'] ?? '') === 'Milieu' ? 'selected' : ''; ?>>Milieu</option>
                    <option value="Attaquant" <?php echo ($joueur_a_modifier['poste'] ?? '') === 'Attaquant' ? 'selected' : ''; ?>>Attaquant</option>
                </select>
            </div>
            <div class="form-group">
                <label for="numero_maillot">Numéro de maillot</label>
                <input type="number" name="numero_maillot" id="numero_maillot" value="<?php echo htmlspecialchars($joueur_a_modifier['numero_maillot'] ?? ''); ?>" required>
            </div>

            <button type="submit" class="btn btn-primary"><?php echo $joueur_a_modifier ? 'Mettre à jour' : 'Ajouter'; ?></button>
            <?php if ($joueur_a_modifier): ?>
                <a href="manager_joueurs.php" class="btn btn-danger">Annuler la modification</a>
            <?php endif; ?>
        </form>
    </div>

    <h2 style="margin-top: 40px; padding-bottom: 10px; border-bottom: 2px solid #eee; margin-bottom: 25px;">Effectif Actuel</h2>
    <table>
        <thead>
        <tr>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Poste</th>
            <th>N° Maillot</th>
            <th>Compte Utilisateur</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($joueurs as $joueur): ?>
            <tr>
                <td><?php echo htmlspecialchars($joueur['nom']); ?></td>
                <td><?php echo htmlspecialchars($joueur['prenom']); ?></td>
                <td><?php echo htmlspecialchars($joueur['poste']); ?></td>
                <td><?php echo htmlspecialchars($joueur['numero_maillot']); ?></td>
                <td class="actions">
                    <?php if ($joueur['id_utilisateur']): ?>
                        <?php if ($joueur['role'] === 'manager'): ?>
                            <span class="badge-manager">Manager</span>
                        <?php endif; ?>

                        <a href="manager_joueurs.php?delete_user_id=<?php echo $joueur['id_utilisateur']; ?>" class="btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer le compte de cet utilisateur ?');">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z"/></svg>
                            Suppr. accès
                        </a>
                    <?php else: ?>
                        <a href="manager_creation_compte.php?id_joueur=<?php echo $joueur['id_joueur']; ?>" class="btn-success">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32V224H48c-17.7 0-32 14.3-32 32s14.3 32 32 32H192V432c0 17.7 14.3 32 32 32s32-14.3 32-32V288H400c17.7 0-32-14.3-32-32s-14.3-32-32-32H256V80z"/></svg>
                            Créer accès
                        </a>
                    <?php endif; ?>
                </td>
                <td class="actions">
                    <a href="manager_joueurs.php?edit_id=<?php echo $joueur['id_joueur']; ?>" class="btn-success">Modifier</a>
                    <a href="manager_joueurs.php?delete_id=<?php echo $joueur['id_joueur']; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce joueur ?');" class="btn-danger">Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

<?php include 'footer.php'; ?>