<!DOCTYPE html>
<html lang="fr">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        nav {
            background-color: #343a40;
        }

        .navbar-brand {
            padding: 14px 16px;
            text-decoration: none;
        }

        #title {
            color: white;
            font-size: 1.5em;
        }

        #buttons {
            margin-left: auto;
        }

        #buttons a,
        #buttons button {
            margin-left: 10px;
        }

        #buttons button {
            padding: 8px 12px;
        }

        #buttons button:hover {
            cursor: pointer;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="accueil.html.php">Accueil</a>
    <a class="navbar-brand" href="catalogue.html.php">Catalogue</a>

    <div id="title" class="mx-auto">CROSL Formations</div>

    <div id="buttons">
        <?php
        session_start();
        if (isset($_SESSION['user'])) {
            echo '<a href="./Functions/deconnexion.php" class="deco" data-tooltip="' . $_SESSION['prenom'] . '">Deconnexion</a>';
        } else {
            echo '<button class="btn btn-light" onclick="redirectToLogin()">Se connecter</button>';
        }
        ?>
    </div>
</nav>

<script>
    function redirectToLogin() {
        window.location.href = 'connexion.html.php';
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
</body>
</html>
