<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        nav {
            background-color: #555;
            overflow: hidden;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
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

        button {
            padding: 10px 20px;
            background-color: #4285f4;
            color: #fff;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            font-size: 1em; /* Taille de police */
        }

        button:hover {
            background-color: #0066cc;
        }

        .deco {
            text-decoration: none;
            padding: 8px 16px;
            background-color: #dc3545;
            color: #fff;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            position: relative;
        }

        .deco:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            top: 60%;
            left: 50%;
            transform: translateX(-50%);
            background-color: #fff;
            border: 1px solid #ddd;
            padding: 5px;
            border-radius: 5px;
            display: block;
            z-index: 1;
        }

        .deco:hover {
            background-color: #bd2130;
        }

        h3 {
            margin: 0;
            color: #ffcc00;
            font-size: 1.2em;
        }
    </style>
</head>
<body>
<nav>
    <a href="accueil.html.php">Accueil</a>
    <a href="catalogue.html.php">Catalogue</a>
    <?php
    session_start();
    if (isset($_SESSION['user'])) {
        if ($_SESSION['role'] == 'A') {
            echo '<a href="administration.html.php">Administration</a>';
        }
    }
    ?>
    <div id="buttons">
        <?php
        if (isset($_SESSION['user'])) {
            echo '<a href="./Functions/deconnexion.php" class="deco" data-tooltip="' . $_SESSION['prenom'] . '">Deconnexion</a>';
        } else {
            echo '<button onclick="redirectToLogin()">Se connecter</button>';
        }
        ?>
    </div>
</nav>
<script>
    function redirectToLogin() {
        window.location.href = 'connexion.html.php';
    }
</script>
</body>
</html>
