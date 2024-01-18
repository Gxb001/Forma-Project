<?php

//Inclure votre fichier de fonctions
include_once 'functions.php';

if (isset($_POST['exportType'])) {

    //Récupérer le type d'exportation
    $exportType = $_POST['exportType'];

    //Initialiser le tableau de participants
    $allParticipants = array();

    if ($exportType === 'allFormations') {
        //Obtenir toutes les formations
        $formations = getFormations();

        foreach ($formations as $formation) {
            //Obtenir les participants de chaque formation
            $participants = getAllUsersFormation($formation['id_formation']);

            //Ajouter les participants au tableau global
            while ($row = $participants->fetch(PDO::FETCH_ASSOC)) {
                $allParticipants[] = $row;
            }
        }

    } elseif ($exportType === 'selectedFormation' && isset($_POST['formationId'])) {
        //Obtenir les participants de la formation spécifique
        $formationId = $_POST['formationId'];
        $participants = getAllUsersFormation($formationId);

        //Convertir les résultats en tableau associatif
        $allParticipants = $participants->fetchAll(PDO::FETCH_ASSOC);
    }

    if (!empty($allParticipants)) {
        //Nom du fichier CSV
        $csvFileName = 'export_participants.csv';

        //En-têtes du fichier CSV
        $csvContent = "Nom,Prenom,Email,Association,Tel,Adresse,Ville,CP,IdFormation,LibelleFormation\n";

        //Ajouter les données au contenu du CSV
        foreach ($allParticipants as $participant) {
            $csvContent .= implode(',', $participant) . "\n";
        }

        //En-tête HTTP pour le téléchargement
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="' . $csvFileName . '"');

        //Sortie du contenu du CSV
        echo $csvContent;
        exit;

    } else {
        //En cas d'erreur, renvoyer une réponse JSON appropriée
        echo json_encode(['participant' => 'Aucun participant à exporter.']);
        exit;
    }
} else {
    //En cas de paramètre exportType non défini, renvoyer une réponse JSON appropriée
    echo json_encode(['error' => 'Paramètre exportType non défini.']);
    exit;
}

?>
