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
    $res = $formations->fetchAll();
    if (count($res) == 0) {
        echo '<p>Nous avons aucune formation à vous proposer à ce jour</p>';
        echo '<button onclick="redirectToAccueil()">Retour à l\'accueil</button>';
    } else {
        foreach ($res as $formation) {
            $iddomaine = $formation['id_domaine'];
            $domaine = getDomaine($iddomaine);
            echo '<div class="formation">';
            echo '<h3>' . $formation['libelle_formation'] . '</h3>';
            echo '<p>Coût : ' . $formation['cout'] . '€ par participant</p>';
            echo '<p>Contenu : ' . $formation['contenu'] . '</p>';
            echo '<p>Domaine : ' . $domaine . '</p>';
            echo '<p>Nombre de places : ' . $formation['nb_place'] . '</p>';
            if (isset($_SESSION['user'])) {
                echo '<button class="btn-inscrire" data-id="' . $formation["id_formation"] . '">S\'inscrire</button>';
            } else {
                echo '<button class="btn-inscrire" onclick="alert(\'Vous devez être connecté pour vous inscrire à une formation.\')">S\'inscrire</button>';
            }
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
<div id="message-container"
     style="position: fixed; top: 10px; right: 10px; padding: 10px; background-color: #4CAF50; color: #fff; display: none;"></div>
<?php
include 'includes/footer.html';
?>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"
        integrity="sha384-UG8ao2jwOWB7/oDdObZc6ItJmwUkR/PfMyt9Qs5AwX7PsnYn1CRKCTWyncPTWvaS"
        crossorigin="anonymous"></script>
<script>
    var moisEnFrancais = [
        'janvier', 'février', 'mars', 'avril', 'mai', 'juin',
        'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'
    ];

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

    function redirectToAccueil() {
        window.location.href = 'accueil.html.php';
    }

    function formatHeure(heure) {
        var date = new Date("1970-01-01T" + heure + "Z");
        var heures = date.getUTCHours();
        var minutes = date.getUTCMinutes();
        var heureFormatee = heures + ":" + (minutes < 10 ? '0' : '') + minutes;
        return heureFormatee;
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
            sessionsFutures.forEach(function (session, index) {
                var dateLimite = new Date(session.date_limite);
                var dateSession = new Date(session.date_session);
                var dateLimiteFormatted = dateLimite.getDate() + ' ' + moisEnFrancais[dateLimite.getMonth()] + ' ' + dateLimite.getFullYear();
                var dateSessionFormatted = dateSession.getDate() + ' ' + moisEnFrancais[dateSession.getMonth()] + ' ' + dateSession.getFullYear();
                var statut = getStatut(<?php echo $_SESSION['id']; ?>, session.id_session); //Définir le statut initial
                var contenuSession = '';
                var nbMax = +session.nb_max;
                var nbParticipants = +session.nb_participant;
                if (session.nb_participant >= session.nb_max) {
                    contenuSession = '<p>Session n°' + (index + 1) + '</p>' +
                        '<p>Session complète</p>';
                } else {
                    var placerestantes = nbMax - nbParticipants;
                    contenuSession =
                        '<p>Session n°' + (index + 1) + '</p>' +
                        '<p>Date limite: ' + dateLimiteFormatted + '</p>' +
                        '<p>Date de session: ' + dateSessionFormatted + '</p>' +
                        '<p>Heure de début: ' + formatHeure(session.heure_debut) + '</p>' +
                        '<p>Heure de fin: ' + formatHeure(session.heure_fin) + '</p>' +
                        '<p>Lieu: ' + session.lieux + '</p>' +
                        '<p>Nombre de place(s): ' + placerestantes + '/' + session.nb_max + '</p>' +
                        '<p>Statut: ' + statut + '</p>' +
                        '<button class="btn-inscrire-session" data-id-session="' + session.id_session + '" data-id-utilisateur="' + <?php echo $_SESSION['id']; ?> +'">S\'inscrire</button>' +
                        '<hr>';
                }
                $('#sessionsModalBody').append(contenuSession);
            });
        } else {
            $('#sessionsModalBody').html('<p>Nous n\'avons pour le moment aucune session à vous proposer pour cette formation.</p>');
        }
        $('.btn-inscrire-session').on('click', function () {
            var idSession = $(this).data('id-session');
            var idUtilisateur = $(this).data('id-utilisateur');
            inscrireSession(idSession, idUtilisateur);
        });
        $('#sessionsModal').modal('show');
    }

    function getStatut(idUtilisateur, idSession) {
        var statut = '';
        $.ajax({
            type: 'GET',
            url: 'Functions/get_statut.php',
            data: {idUtilisateur: idUtilisateur, idSession: idSession},
            async: false,
            success: function (data) {
                switch (data.trim()) {
                    case 'eligible':
                        statut = 'Eligible';
                        break;
                    case 'non-eligible':
                        statut = 'Non éligible';
                        break;
                    case 'En Cours':
                    case 'Acceptée':
                    case 'Refusée':
                        statut = data.trim();
                        break;
                    default:
                        console.error('Statut inconnu :', data);
                        break;
                }
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
        return statut;
    }


    function inscrireSession(idSession, idUtilisateur) {
        console.log('Envoi de la requête avec idSession : ' + idSession + ', idUtilisateur : ' + idUtilisateur);
        $.ajax({
            type: 'POST',
            url: 'Functions/inscrire_session.php',
            data: {
                idSession: idSession,
                idUtilisateur: idUtilisateur
            },
            success: function (data) {
                console.log('Réponse du serveur :', data);
                if (data.includes('success')) {
                    afficherMessage('Demande d\'inscription envoyée avec succès');
                } else if (data.includes('adm-att')) {
                    afficherMessage('Vous êtes administrateur, vous ne pouvez pas vous inscrire à une session');
                } else if (data.includes('ltm-att')) {
                    afficherMessage('Vous avez atteint le nombre maximum d\'inscriptions pour cette année (3)');
                } else if (data.includes('dm-att')) {
                    afficherMessage('Vous avez déjà deux inscriptions dans ce domaine');
                } else if (data.includes('ttr-crs')) {
                    afficherMessage('Votre demande d\'inscription est déjà en cours de traitement');
                } else if (data.includes('ttr-cra')) {
                    afficherMessage('Votre demande d\'inscription a deja été acceptée');
                } else if (data.includes('ss-cmpt')) {
                    afficherMessage('La session est complète');
                } else if (data.includes('error')) {
                    afficherMessage('Une erreur est survenue');
                }
            },
            error: function (xhr, status, error) {
                console.error('Erreur Ajax :', xhr.responseText);
            }
        });
        setTimeout(function () {
            location.reload();
        }, 2000);
    }

    function afficherMessage(message) {
        //Affiche le message dans le coin de l'écran
        var messageContainer = $('#message-container');
        messageContainer.text(message).fadeIn().delay(2000).fadeOut();
    }
</script>
</body>
</html>
