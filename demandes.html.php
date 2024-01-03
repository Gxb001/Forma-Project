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
<?php include 'Includes/navbar.html.php'; ?>

<div class="container mt-5">
    <h2 class="mb-4">Administration des Demandes d'Inscription</h2>

    <?php
    // Vérifie s'il y a des demandes
    if (empty($demandes)) {
        echo '<p>Aucun utilisateur ne s\'est inscrit à une session.</p>';
    } else {
        // Affiche les demandes
        foreach ($demandes as $demande) {
            echo '<div class="card mb-3">';
            echo '<div class="card-body">';
            echo '<p class="card-text">Utilisateur ID: ' . $demande['id_utilisateur'] . '</p>';
            echo '<p class="card-text">Session ID: ' . $demande['id_session'] . '</p>';
            echo '<p class="card-text">Date d\'inscription: ' . $demande['date_inscription'] . '</p>';
            echo '<p class="card-text">État: ' . $demande['etat'] . '</p>';

            // Boutons pour accepter ou refuser la demande
            echo '<button class="btn btn-success" onclick="accepterDemande(' . $demande['id_inscription'] . ')">Accepter</button>';
            echo '<button class="btn btn-danger ms-2" onclick="refuserDemande(' . $demande['id_inscription'] . ')">Refuser</button>';

            echo '</div>';
            echo '</div>';
        }
    }
    ?>

</div>
<?php include 'Includes/footer.html'; ?>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"
        integrity="sha384-UG8ao2jwOWB7/oDdObZc6ItJmwUkR/PfMyt9Qs5AwX7PsnYn1CRKCTWyncPTWvaS"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
<script>
    function accepterDemande(idInscription) {
        // Code pour effectuer l'acceptation de la demande (AJAX, etc.)
        $.ajax({
            type: 'POST',
            url: 'Functions/accepterDemande.php', // Remplace avec le chemin correct de ton script PHP
            data: {idInscription: idInscription},
            success: function (data) {
                // Traiter la réponse du serveur si nécessaire
                console.log(data);
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
    }

    function refuserDemande(idInscription) {
        // Code pour effectuer le refus de la demande (AJAX, etc.)
        $.ajax({
            type: 'POST',
            url: 'Functions/refuserDemande.php', // Remplace avec le chemin correct de ton script PHP
            data: {idInscription: idInscription},
            success: function (data) {
                // Traiter la réponse du serveur si nécessaire
                console.log(data);
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
    }
</script>

</body>
</html>