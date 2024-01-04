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
                <!-- Contenu de la modal -->
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
            sessions = JSON.parse(sessions);
            console.log('Données de sessions après parsing JSON:', sessions);
        } catch (error) {
            console.error('Erreur de parsing JSON:', error);
        }
        $('#sessionsModalBody').empty();
        var sessionsFutures = sessions.filter(function (session) {
            return new Date(session.date_session) >= new Date();
        });
        if (sessionsFutures.length > 0) {
            sessionsFutures.forEach(function (session) {
                var contenuSession = '<p>Date limite: ' + session.date_limite + '</p>' +
                    '<p>Date de session: ' + session.date_session + '</p>' +
                    '<p>Heure de début: ' + session.heure_debut + '</p>' +
                    '<p>Heure de fin: ' + session.heure_fin + '</p>' +
                    '<p>Lieu: ' + session.lieux + '</p>' +
                    '<p>Nombre de participants: ' + session.nb_participant + '</p>' +
                    '<button class="btn-inscrire-session" data-id-session="' + session.id_session + '">S\'inscrire</button>' +
                    '<hr>';
                $('#sessionsModalBody').append(contenuSession);
            });
        } else {
            $('#sessionsModalBody').html('<p>Nous n\'avons pour le moment aucune session à vous proposer pour cette formation.</p>');
        }
        $('.btn-inscrire-session').on('click', function () {
            var idSession = $(this).data('id-session');
            inscrireSession(idSession);
        });
        $('#sessionsModal').modal('show');
    }

    function inscrireSession(idSession) {
        $.ajax({
            type: 'POST',
            url: 'Functions/inscrire_session.php',
            data: {idSession: idSession},
            success: function (data) {
                console.log(data);
                window.location.reload();
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
    }
</script>
</body>
</html>
