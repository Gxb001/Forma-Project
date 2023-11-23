<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CROSL Formations - Catalogue</title>
    <link rel="stylesheet" href="Styles/style_catalogue.css">
    <link rel="stylesheet" href="Styles/Font.css">
</head>

<body>

<?php
include 'includes/header.html.php';
include 'includes/navbar.html.php';
?>

<section>
    <h2>Catalogue des Formations</h2>

    <!-- Exemple de formation 1 -->
    <div class="formation">
        <h3>Formation sur la législation sportive</h3>
        <p>Date: 15-17 janvier 2024</p>
        <p>Lieu: Maison Régionale des Sports, Lorraine</p>
        <p>Intervenant: M. Expert Juridique</p>
        <p>Coût: 50€ par participant</p>
        <?php
        if (isset($_SESSION['user'])) {
            echo '<button>Inscription</button>';
        }
        /*if(fonctionveriflesinscriptionsduclient()){
            echo '<button>Se désinscrire</button>';
        }
        if(fonctionquiverifiequiaplusdeplace()){
            echo '<button disabled>S\'inscrire</button>';
        }*/
        ?>
    </div>

    <!-- Exemple de formation 2 -->
    <div class="formation">
        <h3>Atelier pratique sur le développement durable</h3>
        <p>Date: 22-23 février 2024</p>
        <p>Lieu: Salle Verte, Lorraine</p>
        <p>Intervenants: Mme Écologiste et M. Gestionnaire Énergétique</p>
        <p>Coût: 40€ par participant</p>
        <button>Inscription</button>
    </div>

</section>

<?php
include 'includes/footer.html';
?>

</body>

</html>
