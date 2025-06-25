<?php
ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_NOTICE);

session_start();

// --- Paramètres de connexion à la base de données ---
define('DB_HOST', 'localhost');      // Hôte de la base de données
define('DB_NAME', 'sae23_foot');       // Nom de la base de données
define('DB_USER', 'root');           // Utilisateur de la base de données
define('DB_PASS', '');               // Mot de passe de l'utilisateur

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

/**
 * Fonction pour vérifier si un utilisateur est connecté et a le bon rôle.
 * Redirige vers la page de connexion si l'accès n'est pas autorisé.
 * @param string $roleRequis Le rôle requis pour accéder à la page ('manager' ou 'joueur').
 */
function verifier_acces($roleRequis) {
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== $roleRequis) {
        header("Location: index.php?error=acces_interdit");
        exit();
    }
}
?>
