<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CROSL Formations - Inscription</title>
    <link rel="stylesheet" href="Styles/style_connexion.css">
</head>
<body>
<?php
include 'includes/header.html.php';
include 'includes/navbar.html.php';
?>
<section>
    <h2>Connexion</h2>
    <form action="Functions/VerifConnexion.php" method="post">
        <input type="email" id="email" name="email" required placeholder="Email">
        <input type="password" id="mdp" name="mdp" required placeholder="Mot De Passe">
        </select>
        <button type="submit">Se connecter</button>
    </form>
</section>
<?php
include 'includes/footer.html';
?>
</body>
</html>
