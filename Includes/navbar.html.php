<link rel="stylesheet" href="../Styles/navbar.css">
<nav>
    <a href="accueil.html.php">Accueil</a>
    <a href="catalogue.html.php">Catalogue</a>
    <?php
    session_start();
    if (isset($_SESSION['role'])) {
        if ($_SESSION['role'] == 'admin') {
            echo '<a href="administration.html.php">Administration</a>';
        }
    }
    ?>
    <a href="#">Contact</a>
</nav>