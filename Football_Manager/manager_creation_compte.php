<?php
include_once 'config.php';
verifier_acces('manager');

$error_message = '';
$id_joueur = $_GET['id_joueur'] ?? null;

if (!$id_joueur) {
    header("Location: manager_joueurs.php?error=noplayer");
    exit();
}

$stmt = $pdo->prepare("SELECT nom, prenom FROM joueurs WHERE id_joueur = ?");
$stmt->execute([$id_joueur]);
$joueur = $stmt->fetch();

if (!$joueur) {
    header("Location: manager_joueurs.php?error=playernotfound");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'];
    $password = $_POST['password'];
    $is_manager = isset($_POST['is_manager']);

    if (empty($login) || empty($password)) {
        $error_message = "L'identifiant et le mot de passe sont obligatoires.";
    } else {
        $stmt_check = $pdo->prepare("SELECT id_utilisateur FROM utilisateurs WHERE login = ?");
        $stmt_check->execute([$login]);
        if ($stmt_check->fetch()) {
            $error_message = "Cet identifiant est déjà utilisé. Veuillez en choisir un autre.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = $is_manager ? 'manager' : 'joueur';

            $stmt_insert = $pdo->prepare(
                "INSERT INTO utilisateurs (login, mot_de_passe, role, id_joueur_associe) VALUES (?, ?, ?, ?)"
            );
            $stmt_insert->execute([$login, $hashed_password, $role, $id_joueur]);

            header("Location: manager_joueurs.php?success=1");
            exit();
        }
    }
}


$pageTitle = "Créer un Compte Joueur";
include 'header.php';
?>

    <h1>Création de compte pour <?php echo htmlspecialchars($joueur['prenom'] . ' ' . $joueur['nom']); ?></h1>

<?php if ($error_message): ?>
    <div class="alert alert-danger"><?php echo $error_message; ?></div>
<?php endif; ?>

    <div class="form-container">
        <form action="manager_creation_compte.php?id_joueur=<?php echo $id_joueur; ?>" method="post">
            <div class="form-group">
                <label for="login">Identifiant</label>
                <input type="text" name="login" id="login" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div class="form-group">
                <input type="checkbox" name="is_manager" id="is_manager" value="1" style="width: auto; margin-right: 10px;">
                <label for="is_manager" style="display: inline;">Donner les droits de manager à cet utilisateur</label>
            </div>
            <button type="submit" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32V224H48c-17.7 0-32 14.3-32 32s14.3 32 32 32H192V432c0 17.7 14.3 32 32 32s32-14.3 32-32V288H400c17.7 0-32-14.3-32-32s-14.3-32-32-32H256V80z"/></svg>
                Créer le compte
            </button>
            <a href="manager_joueurs.php" class="btn btn-danger" style="margin-left: 10px;">Annuler</a>
        </form>
    </div>

<?php include 'footer.php'; ?>