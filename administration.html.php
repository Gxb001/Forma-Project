<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CROSL Formations - Administration</title>
    <link rel="stylesheet" href="Styles/style_administration.css">
    <link rel="stylesheet" href="Styles/Font.css">
    <link rel="stylesheet" href="Styles/style_scrollbar.css">
</head>
<body>
<?php
include 'includes/navbar.html.php';
if (isset($_SESSION['user'])) {
    if ($_SESSION['role'] != "A") {
        header("Location: accueil.html.php");
        exit;
    }
}
?>
<section>
    <h2>Tableau de Bord Administratif</h2>
</section>
<?php
include 'includes/footer.html';
?>
</body>
</html>
