<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CROSL Formations - Administration</title>
    <link rel="stylesheet" href="Styles/style_administration.css">
    <link rel="stylesheet" href="Styles/Font.css">
    <link rel="stylesheet" href="Styles/style_scrollbar.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>

<body>
<?php
include 'includes/navbar.html.php';
if (isset($_SESSION['user']) && $_SESSION['role'] != "A") {
    header("Location: accueil.html.php");
    exit;
}
?>
<section>
    <h2>Tableau de Bord Administratif</h2>

    <!-- Bouton pour ouvrir le modal -->
    <button type="button" class="btn btn-outline-secondary btn-sm float-right mt-2 mr-2" data-bs-toggle="modal"
            data-bs-target="#exportModal">
        Exporter les participants
    </button>

    <div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exportModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exportModalLabel">Choisir l'exportation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <!-- Options d'exportation -->
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="exportType" id="exportAll"
                               value="allFormations" checked>
                        <label class="form-check-label" for="exportAll">
                            Exporter toutes les formations de l'année courante avec leurs participants
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="exportType" id="exportSelected"
                               value="selectedFormation">
                        <label class="form-check-label" for="exportSelected">
                            Sélectionner une formation pour exporter ses participants actuellement acceptés
                        </label>
                    </div>

                    <!-- Section pour la sélection de formation -->
                    <div id="selectFormationSection" style="display: none;">
                        <label for="selectFormation">Sélectionner une formation :</label>
                        <select class="form-control" id="selectFormation">
                            <!-- Les options seront ajoutées dynamiquement ici -->
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <!-- Bouton pour déclencher l'exportation -->
                    <button type="button" class="btn btn-primary" id="exportData" data-bs-dismiss="modal">Exporter
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>
<div id="message-container"
     style="position: fixed; top: 10px; right: 10px; padding: 10px; background-color: #4CAF50; color: #fff; display: none;"></div>
<?php
include 'includes/footer.html';
?>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"
        integrity="sha384-UG8ao2jwOWB7/oDdObZc6ItJmwUkR/PfMyt9Qs5AwX7PsnYn1CRKCTWyncPTWvaS"
        crossorigin="anonymous"></script>
<script>
    document.getElementById('exportSelected').addEventListener('change', function () {
        if (this.checked) {
            $.ajax({
                url: 'Functions/getFormations.php',
                method: 'GET',
                dataType: 'json',
                success: function (formations) {
                    console.log(formations);

                    //Ajouter les options de formation au menu déroulant
                    var selectFormation = document.getElementById('selectFormation');
                    selectFormation.innerHTML = "";

                    //Assurez-vous que formations est un tableau avant d'appliquer forEach
                    if (Array.isArray(formations)) {
                        formations.forEach(function (formation) {
                            var option = document.createElement('option');
                            option.value = formation.id_formation;
                            option.text = formation.libelle_formation;
                            selectFormation.add(option);
                        });

                        //Afficher la section de sélection de formation
                        document.getElementById('selectFormationSection').style.display = 'block';
                    } else {
                        console.error('Les données retournées ne sont pas un tableau d\'objets.', formations);
                    }
                },
                error: function (error) {
                    console.error('Erreur lors de la requête AJAX', error);
                }
            });
        } else {
            document.getElementById('selectFormationSection').style.display = 'none';
        }
    });


    document.getElementById('exportData').addEventListener('click', function () {
        //Récupérer la valeur de l'option choisie
        var exportType = document.querySelector('input[name="exportType"]:checked').value;

        //Exécuter la logique d'exportation en fonction du choix
        if (exportType === 'allFormations') {
            console.log('Exportation de toutes les formations');
            $.ajax({
                url: 'Functions/exportParticipants.php',
                method: 'POST',
                data: {exportType: 'allFormations'},
                success: function (response) {
                    console.log('Réponse du serveur (participants de toutes les formations):', response);
                    if (response.includes('participant')) {
                        afficherMessage('Aucun participant à exporter.');
                    } else if (response.includes('error')) {
                        afficherMessage('Erreur lors de l\'exportation.');
                    } else {
                        var blob = new Blob([response], {type: 'application/csv'});
                        var url = window.URL.createObjectURL(blob);
                        var a = document.createElement('a');
                        a.href = url;
                        a.download = 'export_participants.csv';
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                    }
                },
                error: function (error) {
                    console.error('Erreur lors de la requête AJAX', error);
                }
            });
        } else if (exportType === 'selectedFormation') {
            console.log('Exportation de la formation sélectionnée');
            var selectedFormationId = document.getElementById('selectFormation').value;
            $.ajax({
                url: 'Functions/exportParticipants.php',
                method: 'POST',
                data: {exportType: 'selectedFormation', formationId: selectedFormationId},
                success: function (response) {
                    console.log('Réponse du serveur (participants de la formation sélectionnée):', response);
                    if (response.includes('participant')) {
                        afficherMessage('Aucun participant à exporter.');
                    } else if (response.includes('error')) {
                        afficherMessage('Erreur lors de l\'exportation.');
                    } else {
                        var blob = new Blob([response], {type: 'application/csv'});
                        var url = window.URL.createObjectURL(blob);
                        var a = document.createElement('a');
                        a.href = url;
                        a.download = 'export_participants.csv';
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                    }
                },
                error: function (error) {
                    console.error('Erreur lors de la requête AJAX', error);
                }
            });
        }

        //Fermer le modal après l'exportation
        document.getElementById('exportModal').style.display = 'none';
    });

    function afficherMessage(message) {
        //Affiche le message dans le coin de l'écran
        var messageContainer = $('#message-container');
        messageContainer.text(message).fadeIn().delay(2000).fadeOut();
    }


</script>

</body>
</html>
