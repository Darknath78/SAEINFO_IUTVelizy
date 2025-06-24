<?php
include_once 'config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $_POST['login'];
    $password = $_POST['password'];

    if (empty($login) || empty($password)) {
        $error_message = "Veuillez remplir tous les champs.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE login = ?");
        $stmt->execute([$login]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['mot_de_passe'])) {
            $_SESSION['user_id'] = $user['id_utilisateur'];
            $_SESSION['user_login'] = $user['login'];
            $_SESSION['user_role'] = $user['role'];

            header("Location: dashboard.php");
            exit();
        } else {
            $error_message = "Identifiant ou mot de passe incorrect.";
        }
    }
}

$pageTitle = "Connexion";
include 'header.php';
?>

<div class="form-container">
    <h1>Connexion</h1>
    <p>Connectez-vous pour accéder à votre espace.</p>

    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <form action="index.php" method="post">
        <div class="form-group">
            <label for="login">Identifiant</label>
            <input type="text" id="login" name="login" required>
        </div>
        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Se connecter</button>
    </form>
    <p style="margin-top: 20px;">
        Pas de compte ? <a href="public.php">Consultez les informations publiques</a>.
    </p>
</div>

<?php include 'footer.php'; ?>
