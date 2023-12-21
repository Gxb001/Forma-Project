<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CROSL Formations - Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="Styles/style_scrollbar.css">
    <link rel="stylesheet" href="Styles/style_connexion.css">
    <link rel="stylesheet" href="Styles/Font.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        section {
            max-width: 400px;
            margin: auto;
            margin-top: 50px;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #343a40;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        input {
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            width: 100%;
        }

        button {
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        #error_login {
            color: #dc3545;
            text-align: center;
        }
    </style>
</head>
<body>
<?php include 'includes/navbar.html.php'; ?>

<?php
if (isset($_SESSION['user']) && $_SESSION['user'] == "authentified") {
    header('Location: accueil.html.php');
}
?>

<section>
    <h2>Connexion</h2>
    <form action="Functions/VerifConnexion.php" method="post">
        <input type="email" id="email" name="email" required placeholder="Email">
        <input type="password" id="mdp" name="mdp" required placeholder="Mot De Passe">
        <div id="error_login"></div>
        <button type="submit">Se connecter</button>
    </form>
</section>

<?php include 'includes/footer.html'; ?>

<script>
    const urlParams = new URLSearchParams(window.location.search);
    const data = urlParams.get('data');
    if (data === "activate_logger") {
        const errorDiv = document.getElementById("error_login");
        errorDiv.innerHTML = "Login ou mot de passe incorrect !";
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
</body>
</html>
