<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CROSL Formations - Catalogue</title>
    <link rel="stylesheet" href="Styles/style_catalogue.css">
    <link rel="stylesheet" href="Styles/Font.css">
    <link rel="stylesheet" href="Styles/style_scrollbar.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>
<?php
include 'includes/navbar.html.php';
include 'Functions/functions.php';
?>

<section class="formations-container">
    <h2>Catalogue des Formations</h2>
    <?php
    //récupération des formations
    $formations = getFormations();
    //récupération de toutes les lignes de résultat
    $res = $formations->fetchAll();
    //affichage des formations, libelle, cout, contenu, objectif et nombre de place
    if (count($res) == 0) {
        echo '<p>Nous avons aucune formation à vous proposer à ce jour</p>';
        echo '<button onclick="redirectToAccueil()">Retour à l\'accueil</button>';
    } else {
        // afficher le domaine de la formation à faire
        foreach ($res as $formation) {//afficher le nombre de places restantes
            echo '<div class="formation">';
            echo '<h3>' . $formation['libelle_formation'] . '</h3>';
            echo '<p>Coût: ' . $formation['coût'] . '€ par participant</p>';
            echo '<p>Contenu: ' . $formation['contenu'] . '</p>';
            echo '<p>Objectif: ' . $formation['objectif'] . '</p>';
            echo '<p>Nombre de places: ' . $formation['nb_place'] . '</p>';
            if (isset($_SESSION['user'])) {
                if ($_SESSION['user'] == "authentified") {
                    $idformation = $formation["id_formation"];
                    echo '<button onclick="openModal(' . $idformation . ')">S\'inscrire</button>';
                }
            } else {
                echo '<button onclick="information()">S\'nscrire</button>';
            }
            echo '</div>';
        }
    }
    ?>
</section>
<section>
    <!-- Ajoutez cela où vous voulez dans votre fichier HTML -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Inscription</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Contenu du modal -->
                    <p id="modalContent"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
include 'includes/footer.html';
?>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"
        integrity="sha384-r7npnvBRvkbw5VTuT7hBzy2pM8Tf8aIe4OH/GX65GgY4V4fwjX3r/JQqI8V0aVad"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
<script>
    function information() {
        alert("Vous devez être connecté pour vous inscrire à une formation.");
    }

    function redirectToAccueil() {
        window.location.href = 'accueil.html.php';
    }

    function openModal(data) {
        document.getElementById('modalContent').innerText = data;
        $('#myModal').modal('show');
    }
</script>
</body>
</html>