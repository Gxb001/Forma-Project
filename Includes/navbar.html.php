<head>
    <meta charset="UTF-8">
    <style>
        nav {
            background-color: #555;
            overflow: hidden;
        }

        nav a {
            float: left;
            display: block;
            color: white;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
        }

        nav a:hover {
            background-color: #ddd;
            color: black;
        }
    </style>
</head>
<nav>
    <a href="accueil.html.php">Accueil</a>
    <a href="catalogue.html.php">Catalogue</a>
    <?php
    if (isset($_SESSION['user'])) {
        if ($_SESSION['role'] == 'admin') {
            echo '<a href="administration.html.php">Administration</a>';
        }
    }
    ?>
</nav>