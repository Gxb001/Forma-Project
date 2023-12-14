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
include 'Functions/functions.php';
?>

<section>
    <h2>Catalogue des Formations</h2>
    <?php
    //recuperation des formations
    $formations = getFormations();
    //affichage des formations, libelle, cout, contenu, objectif et nombre de place
    if (count($formations->fetchall()) == 0) {
        echo '<p>Nous avons aucune formations à vous proposer à ce jour</p>';
        echo '<button onclick="redirectToAccueil()">Retour à l\'accueil</button>';
    } else {
        //afficher le domaine de la formation (trier)

        foreach ($formations as $formation) {
            echo '<div class="formation">';
            echo '<h3>' . $formation['libelle_formation'] . '</h3>';
            echo '<p>Coût: ' . $formation['coût'] . '€ par participant</p>';
            echo '<p>Contenu: ' . $formation['contenu'] . '</p>';
            echo '<p>Objectif: ' . $formation['objectif'] . '</p>';
            echo '<p>Nombre de places: ' . $formation['nb_place'] . '</p>';
            if (isset($_SESSION['user'])) {
                if ($_SESSION['user'] == "authentified") {
                    echo '<button>Inscription</button>';//recuperer id de la formation du bouton
                }
            } else {
                echo '<button onclick="information()">Inscription</button>';
            }
            /*if(fonctionveriflesinscriptionsduclient(id de la formation)){ verif si le client est inscrit
                echo '<button>Se désinscrire</button>';
            }
            if(fonctionquiverifiequiaplusdeplace(id de la formation)){ verif le nombre de places restantes
                echo '<button disabled>S\'inscrire</button>';
            }*/
            echo '</div>';
        }
    }
    ?>
</section>

<?php
include 'includes/footer.html';
?>
<script>
    function information() {
        alert("Vous devez être connecté pour vous inscrire à une formation.");
    }

    function redirectToAccueil() {
        window.location.href = 'accueil.html.php';
    }
</script>
</body>
</html>
