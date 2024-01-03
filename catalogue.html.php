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
        foreach ($res as $formation) {
            echo '<div class="formation">';
            echo '<h3>' . $formation['libelle_formation'] . '</h3>';
            echo '<p>Coût: ' . $formation['coût'] . '€ par participant</p>';
            echo '<p>Contenu: ' . $formation['contenu'] . '</p>';
            echo '<p>Objectif: ' . $formation['objectif'] . '</p>';
            echo '<p>Nombre de places: ' . $formation['nb_place'] . '</p>';

            // Ajouter un attribut data-id avec l'ID de la formation
            echo '<button class="btn-inscrire" data-id="' . $formation["id_formation"] . '">S\'inscrire</button>';

            echo '</div>';
        }
    }
    ?>
</section>
<div class="modal fade" id="sessionsModal" tabindex="-1" aria-labelledby="sessionsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sessionsModalLabel">Sessions de formation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="sessionsModalBody">
                <!-- Le contenu des sessions sera ajouté ici par JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<?php
include 'includes/footer.html';
?>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"
        integrity="sha384-UG8ao2jwOWB7/oDdObZc6ItJmwUkR/PfMyt9Qs5AwX7PsnYn1CRKCTWyncPTWvaS"
        crossorigin="anonymous"></script>
<script>
    $(document).ready(function () {
        $('.btn-inscrire').on('click', function () {
            var idFormation = $(this).data('id');

            $.ajax({
                url: 'Functions/charger_sessions.php',
                method: 'POST',
                data: {idFormation: idFormation},
                success: function (data) {
                    console.log('Sessions pour la formation ' + idFormation + ':', data);
                    afficherSessionsDansModal(data);
                },
                error: function (xhr, status, error) {
                    console.error(error);
                }
            });
        });
    });

    function information() {
        alert("Vous devez être connecté pour vous inscrire à une formation.");
    }

    function redirectToAccueil() {
        window.location.href = 'accueil.html.php';
    }

    function afficherSessionsDansModal(sessions) {
        console.log('Données de sessions avant parsing JSON:', sessions);

        try {
            // Essaye de convertir les données en objet JSON
            sessions = JSON.parse(sessions);
            console.log('Données de sessions après parsing JSON:', sessions);
        } catch (error) {
            console.error('Erreur de parsing JSON:', error);
        }

        // Vide le contenu actuel de la modal
        $('#sessionsModalBody').empty();

        var sessionsFutures = sessions.filter(function (session) {
            // Filtre les sessions dont la date est aujourd'hui ou plus tard
            return new Date(session.date_session) >= new Date();
        });

        if (sessionsFutures.length > 0) {
            // Ajoute les données des sessions futures dans la modal
            sessionsFutures.forEach(function (session) {
                var contenuSession = '<p>Date limite: ' + session.date_limite + '</p>' +
                    '<p>Date de session: ' + session.date_session + '</p>' +
                    '<p>Heure de début: ' + session.heure_debut + '</p>' +
                    '<p>Heure de fin: ' + session.heure_fin + '</p>' +
                    '<p>Lieu: ' + session.lieux + '</p>' +
                    '<p>Nombre de participants: ' + session.nb_participant + '</p>' +
                    '<button class="btn-inscrire-session" data-id-session="' + session.id_session + '">S\'inscrire</button>' +
                    '<hr>'; // Ajoute une ligne de séparation entre les sessions
                $('#sessionsModalBody').append(contenuSession);
            });
        } else {
            // Affiche un message si aucune session future n'est disponible
            $('#sessionsModalBody').html('<p>Nous n\'avons pour le moment aucune session à vous proposer pour cette formation.</p>');
        }

        // Associe un événement au clic sur le bouton "S'inscrire"
        $('.btn-inscrire-session').on('click', function () {
            // Récupère l'identifiant de la session associé au bouton
            var idSession = $(this).data('id-session');

            // Appelle la fonction d'inscription en passant l'identifiant de la session
            inscrireSession(idSession);
        });

        // Ouvre la modal
        $('#sessionsModal').modal('show');
    }

    // Fonction d'inscription à une session
    function inscrireSession(idSession) {
        // TODO: Implémenter la logique d'inscription ici
        console.log('Inscription à la session avec l\'ID : ' + idSession);
        // ... (ajoutez votre logique d'inscription ici)
    }
</script>

</body>
</html>
