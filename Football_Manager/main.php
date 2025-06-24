<html>
<head>
    <title>TP Informatique sur les bases de données</title>
</head>
<body>
<body bgcolor="#0099FF" text="#000000" link="#FFFFFF">
<center>
    <h1> TP d'informatique sur les bases de données </h1>
    <h3>Gestion de la table GITES </h3>
    <hr width="75%">
    <form name="form" method="post" action="main.php">
        <table width="54%" border="0" height="225" align="center" cellspacing="1">
            <tr>
                <td width="25%" height="99"><center> les régions et les villes et les numeros
                        pour les villes qui ne contient pas </center></td>
            </tr>
            <tr>
                <td width="25%" height="34">
                    <center>
                        <input type="text" name="varrequete">
                    </center>
                </td>
            </tr>
            <tr>
                <td width="25%" height="65">
                    <center>
                        <input type="submit" name="okrequete" value="Exécuter">
                    </center>
                </td>
            </tr>
        </table>
    </form>
    <hr width="75%">
    <?php
    if (isset($_POST['okrequete'])) {
        $varrequete = $_POST['varrequete'] ?? '';
        include ("requete.php");
    } else {
        echo "<b><h4>Sélectionner une requête</h4></b>";
    }
    ?>
</center>
</body>
</html