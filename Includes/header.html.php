<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        #logo {
            flex: 1;
            max-width: 200px;
            height: auto;
        }

        #buttons {
            display: flex;
            gap: 10px;
        }

        button {
            padding: 8px 16px;
            background-color: #fff;
            color: #333;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #ddd;
        }
    </style>
</head>
<body>
<header>
    <img id="logo" src="Medias/logo.png" alt="Logo">
    <div id="buttons">
        <?php
        if (isset($_SESSION['role'])) {
            echo '<button>Connecté</button>';
        } else {
            echo '    
        <button onclick="redirectToLogin()">Se connecter</button>
        <button onclick="redirectToSignUp()">S\'inscrire</button>
    ';
        }
        ?>
    </div>
</header>
</body>
<script>
    function redirectToLogin() {
        window.location.href = 'connexion.html.php';
    }

    function redirectToSignUp() {
        window.location.href = 'inscription.html.php';
    }
</script>
</html>
