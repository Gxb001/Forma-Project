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
<style>
    .dashboard-buttons {
        margin-bottom: 20px;
    }

    .dashboard-buttons button {
        margin-right: 10px;
    }

    #CreateSessionTab {
        display: none;
    }

    #DelFormationTab {
        display: none;
    }

    #DelSessionTab {
        display: none;
    }
</style>
<body>
<?php
include 'includes/navbar.html.php';
if ((isset($_SESSION['user']) && $_SESSION['role'] != "A") || !isset($_SESSION['user'])) {
    header("Location: accueil.html.php");
    exit;
}
?>

<section>
    <h2>Tableau de Bord Administratif</h2>

    <!-- Bouton pour ouvrir le modal -->
    <button type="button" class="btn btn-outline-secondary btn-sm float-end mt-2 mr-2" data-bs-toggle="modal"
            data-bs-target="#exportModal">
        Exporter les participants
    </button>


    <!-- Boutons du tableau de bord -->
    <div class="dashboard-buttons">
        <button type="button" class="btn btn-primary" onclick="afficherDiv('CreateFormationTab')">Créer une formation
        </button>
        <button type="button" class="btn btn-primary" onclick="afficherDiv('CreateSessionTab')">Créer une session
        </button>
        <button type="button" class="btn btn-primary" onclick="afficherDiv('DelFormationTab')">Supprimer une formation
        </button>
        <button type="button" class="btn btn-primary" onclick="afficherDiv('DelSessionTab')">Supprimer une session
        </button>
        <button type="button" class="btn btn-primary" onclick="afficherDiv('UpdateFormationTab')">Modifier une
            formation
        </button>
        <button type="button" class="btn btn-primary" onclick="afficherDiv('UpdateSessionTab')">Modifier une session
        </button>
    </div>


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

<section class="container mt-5 d-flex justify-content-center">
    <!-- Formulaire pour créer une formation -->
    <div class="col-md-6" id="CreateFormationTab">
        <h2>Formation</h2>
        <form id="formFormation" class="card p-4">
            <div class="mb-3">
                <label for="libelle" class="form-label">Libellé:</label>
                <input type="text" class="form-control" name="libelle" required>
            </div>
            <div class="mb-3">
                <label for="cout" class="form-label">Coût:</label>
                <input type="number" class="form-control" name="cout" required>
            </div>
            <div class="mb-3">
                <label for="contenu" class="form-label">Contenu:</label>
                <input type="text" class="form-control" name="contenu" required>
            </div>
            <div class="mb-3">
                <label for="nb_place" class="form-label">Nombre de places:</label>
                <input type="number" class="form-control" name="nb_place" required>
            </div>
            <div class="mb-3">
                <label for="id_domaine" class="form-label">Domaine:</label>
                <?php
                include_once("Functions/functions.php");
                $connexion = obtenirConnexion();
                try {
                    $domaines = $connexion->query("SELECT * FROM domaines");
                    $domaines = $domaines->fetchAll();
                    echo "<select class='form-select' name='id_domaine'>";
                    foreach ($domaines as $domaine) {
                        echo "<option value='" . $domaine['id_domaine'] . "'>" . $domaine['libelle_domaine'] . "</option>";
                    }
                } catch (PDOException $e) {
                    echo $e->getMessage();
                } finally {
                    $connexion = null;
                    echo '</select>';
                }
                ?>
            </div>
            <button type="button" class="btn btn-primary" onclick="creerFormation()">Créer Formation</button>
        </form>
    </div>

    <!-- Formulaire pour créer une session -->
    <div class="col-md-6" id="CreateSessionTab">
        <h2>Session</h2>
        <form id="formSession" class="card p-4">
            <div class="mb-3">
                <label for="date_session" class="form-label">Date de session:</label>
                <input type="date" class="form-control" name="date_session" required>
            </div>
            <div class="mb-3">
                <label for="heure_debut" class="form-label">Heure de début:</label>
                <input type="time" class="form-control" name="heure_debut" required>
            </div>
            <div class="mb-3">
                <label for="heure_fin" class="form-label">Heure de fin:</label>
                <input type="time" class="form-control" name="heure_fin" required>
            </div>
            <div class="mb-3">
                <label for="lieux" class="form-label">Lieux:</label>
                <input type="text" class="form-control" name="lieux" required>
            </div>
            <div class="mb-3">
                <label for="date_limite" class="form-label">Date limite:</label>
                <input type="date" class="form-control" name="date_limite" required>
            </div>
            <div class="mb-3">
                <label for="nbmax" class="form-label">Nombre maximum de participants:</label>
                <input type="number" class="form-control" name="nbmax" required>
            </div>
            <div class="mb-3">
                <label for="id_formation" class="form-label">Formation:</label>
                <?php
                include_once("Functions/functions.php");
                $formations = getFormations();
                $formations = $formations->fetchAll();
                echo "<select class='form-select' name='id_formation'>";
                if (count($formations) == 0) {
                    echo "<option value='N/A'>Aucune formation</option>";
                } else {
                    foreach ($formations as $formation) {
                        echo "<option value='" . $formation['id_formation'] . "'>" . $formation['libelle_formation'] . "</option>";
                    }
                }
                echo '</select>';
                ?>
            </div>
            <button type="button" class="btn btn-primary" onclick="creerSession()">Créer Session</button>
        </form>
    </div>
    <!-- Formulaire pour supprimer une formation -->
    <div class="col-md-6" id="DelFormationTab">
        <h2>Formation</h2>
        <form id="formSupprimerFormation" class="card p-4">
            <div class="mb-3">
                <label for="selectFormationSuppression">Sélectionner une formation à supprimer :</label>
                <select class="form-select" id="selectFormationSuppression">
                    <?php
                    include_once("Functions/functions.php");
                    $formations = getFormations();
                    $formations = $formations->fetchAll();
                    if (count($formations) > 0) {
                        foreach ($formations as $formation) {
                            echo "<option value='" . $formation['id_formation'] . "'>" . $formation['libelle_formation'] . "</option>";
                        }
                    } else {
                        echo "<option value=''>Aucune formation disponible</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="button" class="btn btn-danger" onclick="supprimerFormation()">Supprimer Formation</button>
        </form>
    </div>
    <!-- Formulaire pour supprimer une session -->
    <div class="col-md-6" id="DelSessionTab">
        <h2>Session</h2>
        <form id="formSupprimerSession" class="card p-4">
            <div class="mb-3">
                <label for="selectFormationSuppressionSession">Sélectionner une formation :</label>
                <select class="form-select" id="selectFormationSuppressionSession" onchange="chargerSessions()">
                    <?php
                    include_once("Functions/functions.php");
                    $formations = getFormations();
                    $formations = $formations->fetchAll();
                    if (count($formations) > 0) {
                        foreach ($formations as $formation) {
                            echo "<option value='" . $formation['id_formation'] . "'>" . $formation['libelle_formation'] . "</option>";
                        }
                    } else {
                        echo "<option value=''>Aucune formation disponible</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="selectSessionSuppression">Sélectionner une session à supprimer :</label>
                <select class="form-select" id="selectSessionSuppression">
                    <!-- Les options seront ajoutées dynamiquement ici -->
                </select>
            </div>
            <button type="button" class="btn btn-danger" onclick="supprimerSession()">Supprimer Session</button>
        </form>
    </div>
</section>


<div id="message-container"
     style="position: fixed; top: 10px; right: 10px; padding: 10px; background-color: #4CAF50; color: #fff; display: none;"></div>
<?php include 'includes/footer.html'; ?>
<?php include 'includes/loading.html'; ?>

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
                        if (formations.length !== 0) {
                            formations.forEach(function (formation) {
                                var option = document.createElement('option');
                                option.value = formation.id_formation;
                                option.text = formation.libelle_formation;
                                selectFormation.add(option);
                            });
                        } else {
                            var option = document.createElement('option');
                            option.value = "";
                            option.text = "Aucune formation";
                            selectFormation.add(option);
                        }

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

    document.getElementById('exportAll').addEventListener('change', function () {
        if (this.checked) {
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

    var moisEnFrancais = [
        'janvier', 'février', 'mars', 'avril', 'mai', 'juin',
        'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'
    ];


    //Fonction pour charger les sessions en fonction de la formation sélectionnée
    function chargerSessions() {
        var idFormation = $('#selectFormationSuppressionSession').val();
        if (idFormation) {
            $.ajax({
                url: 'Functions/getSessionsByFormation.php',
                method: 'GET',
                data: {idFormation: idFormation},
                dataType: 'json',
                success: function (sessions) {
                    var selectSession = document.getElementById('selectSessionSuppression');
                    selectSession.innerHTML = "";

                    if (Array.isArray(sessions) && sessions.length > 0) {
                        sessions.forEach(function (session) {
                            date = new Date(session.date_session);
                            dateSessionFormat = date.getDate() + ' ' + moisEnFrancais[date.getMonth()] + ' ' + date.getFullYear();
                            var option = document.createElement('option');
                            option.value = session.id_session;
                            option.text = dateSessionFormat + ' à ' + session.lieux + ' de ' + formatHeure(session.heure_debut) + ' à ' + formatHeure(session.heure_fin);
                            selectSession.add(option);
                        });
                    } else {
                        var option = document.createElement('option');
                        option.value = "N/A";
                        option.text = "Aucune session disponible";
                        selectSession.add(option);
                    }
                },
                error: function (error) {
                    console.error('Erreur lors de la requête AJAX', error);
                }
            });
        }
    }

    var allTabs = ["CreateFormationTab", "CreateSessionTab", "DelFormationTab", "DelSessionTab", "UpdateFormationTab", "UpdateSessionTab"];

    function afficherDiv(id) {
        var div = document.getElementById(id);
        if (div) {
            for (var i = 0; i < allTabs.length; i++) {
                if (allTabs[i] !== id) {
                    var otherDiv = document.getElementById(allTabs[i]);
                    if (otherDiv) {
                        otherDiv.style.display = "none";
                    }
                }
            }
            div.style.display = "block";
        } else {
            console.error("L'élément avec l'ID " + id + " n'a pas été trouvé.");
        }
    }


    //Fonction Ajax pour créer une formation EN TEST
    function creerFormation() {
        $.ajax({
            type: "POST",
            url: "Functions/actions.php",
            data: $("#formFormation").serialize() + "&action=creerFormation",
            success: function (response) {
                if (response.includes("valide")) {
                    afficherMessage("Formation créée avec succès");
                } else if (response.includes("Champs")) {
                    afficherMessage("Veuillez remplir tous les champs");
                } else if (response.includes("Cout")) {
                    afficherMessage("Le cout doit être supérieur à 0");
                } else if (response.includes("Place")) {
                    afficherMessage("Le nombre de place doit être supérieur à 0");
                } else if (response.includes("errf")) {
                    afficherMessage("Erreur lors de la création de la formation");
                }
            }
        });
        setTimeout(function () {
            document.getElementById('formFormation').reset();
        }, 1000);
        setTimeout(function () {
            location.reload();
        }, 1000);
    }

    //Fonction Ajax pour créer une session EN TEST
    function creerSession() {
        $.ajax({
            type: "POST",
            url: "Functions/actions.php",
            data: $("#formSession").serialize() + "&action=creerSession",
            success: function (response) {
                if (response.includes("valide")) {
                    afficherMessage("Session créée avec succès");
                } else if (response.includes("Formation")) {
                    afficherMessage("Aucune formation disponible");
                } else if (response.includes("Champs")) {
                    afficherMessage("Veuillez remplir tous les champs");
                } else if (response.includes("Heure")) {
                    afficherMessage("L'heure de début doit être inférieur à l'heure de fin");
                } else if (response.includes("Date")) {
                    afficherMessage("La date limite d'inscription doit être inférieur à la date de session");
                } else if (response.includes("NbMax")) {
                    afficherMessage("Le nombre de place doit être supérieur à 0");
                } else if (response.includes("err")) {
                    afficherMessage("Erreur lors de la création de la session");
                }
            }
        });
        setTimeout(function () {
            document.getElementById('formSession').reset();
        }, 1000);
        setTimeout(function () {
            location.reload();
        }, 1000);
    }

    //Fonction Ajax pour supprimer une formation
    function deleteFormation(id_formation) {
        $.ajax({
            type: "POST",
            url: "Functions/actions.php",
            data: "id_formation=" + id_formation + "&action=deleteFormation",
            success: function (response) {
                if (response.includes("valide")) {
                    afficherMessage("Formation supprimée avec succès");
                } else if (response.includes("err")) {
                    afficherMessage("Erreur lors de la suppression de la formation");
                } else if (response.includes("Champs")) {
                    afficherMessage("Veuillez spécifier l'ID de la formation à supprimer");
                }
            }
        });
    }

    //Fonction Ajax pour supprimer une session
    function deleteSession(id_session) {
        $.ajax({
            type: "POST",
            url: "Functions/actions.php",
            data: "id_session=" + id_session + "&action=deleteSession",
            success: function (response) {
                if (response.includes("valide")) {
                    afficherMessage("Session supprimée avec succès");
                } else if (response.includes("Champs")) {
                    afficherMessage("Veuillez choisir une session");
                } else if (response.includes("err")) {
                    afficherMessage("Erreur lors de la suppression de la session");
                } else if (response.includes("id")) {
                    afficherMessage("Veuillez spécifier l'ID de la session à supprimer");
                }
            }
        });
    }

    //Gestionnaire d'événements pour le bouton de suppression de formation
    function supprimerFormation() {
        var idFormation = $('#selectFormationSuppression').val();
        if (idFormation) {
            deleteFormation(idFormation);
        } else {
            afficherMessage("Veuillez sélectionner une formation à supprimer.");
        }
        setTimeout(function () {
            location.reload();
        }, 2000);
    }

    // Gestionnaire d'événements pour le bouton de suppression de session
    function supprimerSession() {
        var idSession = $('#selectSessionSuppression').val();
        if (idSession) {
            deleteSession(idSession);
        } else {
            afficherMessage("Veuillez sélectionner une session à supprimer.");
        }
        setTimeout(function () {
            location.reload();
        }, 2000);
    }

    function updateFormation() {
        $.ajax({
            type: "POST",
            url: "Functions/actions.php",
            data: $("#formFormation").serialize() + "&action=updateFormation",
            success: function (response) {
            }
        });
    }

    function formatHeure(heure) {
        var date = new Date("1970-01-01T" + heure + "Z");
        var heures = date.getUTCHours();
        var minutes = date.getUTCMinutes();
        var heureFormatee = heures + ":" + (minutes < 10 ? '0' : '') + minutes;
        return heureFormatee;
    }

    function afficherMessage(message) {
        //Affiche le message dans le coin de l'écran
        var messageContainer = $('#message-container');
        messageContainer.text(message).fadeIn().delay(2000).fadeOut();
    }


    chargerSessions();
</script>
<script>
    window.addEventListener('load', function () {
        document.getElementById('loader-container').style.display = "none";
    });
    window.addEventListener('beforeunload', function () {
        document.getElementById('loader-container').style.display = "flex";
    });
</script>
</body>
</html>
