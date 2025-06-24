<?php
$motDePasseEnClair = 'joueur_pass';

$hash = password_hash($motDePasseEnClair, PASSWORD_DEFAULT);

echo "Le mot de passe en clair est : " . $motDePasseEnClair . "<br>";
echo "Voici son hash à copier dans la base de données : <br>";
echo "<strong>" . $hash . "</strong>";
?>