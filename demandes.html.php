<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration des Demandes d'Inscription</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>
<?php include 'Includes/navbar.html.php';
include "Functions/functions.php" ?>

<div class="container mt-5">
    <h2 class="mb-4">Administration des Demandes d'Inscription</h2>

    <?php
    $demandes = getDemandesInscriptionsEnCours();
    if (empty($demandes)) {
        echo '<p>Aucun utilisateur ne s\'est inscrit à une session.</p>';
    } else {
        // Affiche les demandes
        foreach ($demandes as $demande) {
            echo '<div class="card mb-3">';
            echo '<div class="card-body">';

            // Vérifier si les clés existent avant de les utiliser
            echo '<p class="card-text">Utilisateur ID: ' . (isset($demande['id_utilisateur']) ? $demande['id_utilisateur'] : 'N/A') . '</p>';
            echo '<p class="card-text">Session ID: ' . (isset($demande['id_session']) ? $demande['id_session'] : 'N/A') . '</p>';
            echo '<p class="card-text">Date d\'inscription: ' . (isset($demande['date_inscription']) ? $demande['date_inscription'] : 'N/A') . '</p>';
            echo '<p class="card-text">État: ' . (isset($demande['etat']) ? $demande['etat'] : 'N/A') . '</p>';

            echo '<button class="btn btn-success" onclick="accepterDemande(' . (isset($demande['id_session']) ? $demande['id_session'] : 'N/A') . ', ' . (isset($demande['id_utilisateur']) ? $demande['id_utilisateur'] : 'N/A') . ')">Accepter</button>';
            echo '<button class="btn btn-danger ms-2" onclick="refuserDemande(' . (isset($demande['id_session']) ? $demande['id_session'] : 'N/A') . ', ' . (isset($demande['id_utilisateur']) ? $demande['id_utilisateur'] : 'N/A') . ')">Refuser</button>';

            echo '</div>';
            echo '</div>';
        }
    }
    ?>


</div>
<div id="message-container"
     style="position: fixed; top: 10px; right: 10px; padding: 10px; background-color: #4CAF50; color: #fff; display: none;"></div>
<?php include 'Includes/footer.html'; ?>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"
        integrity="sha384-UG8ao2jwOWB7/oDdObZc6ItJmwUkR/PfMyt9Qs5AwX7PsnYn1CRKCTWyncPTWvaS"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
<script>
    function afficherMessage(message) {
        // Affiche le message dans le coin de l'écran
        var messageContainer = $('#message-container');
        messageContainer.text(message).fadeIn().delay(2000).fadeOut();
    }

    function accepterDemande(idSession, idUtilisateur) {
        $.ajax({
            type: 'POST',
            url: 'Functions/accepterDemande.php',
            data: {
                idSession: idSession,
                idUtilisateur: idUtilisateur
            },
            success: function (data) {
                console.log(data);
                afficherMessage('Demande d\'inscription acceptée avec succès');
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
    }

    function refuserDemande(idSession, idUtilisateur) {
        $.ajax({
            type: 'POST',
            url: 'Functions/refuserDemande.php',
            data: {
                idSession: idSession,
                idUtilisateur: idUtilisateur
            },
            success: function (data) {
                console.log(data);
                afficherMessage('Demande d\'inscription refusée avec succès');
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
    }
</script>
</body>
</html>