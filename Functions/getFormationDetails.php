<?php
include 'functions.php';
if (isset($_GET['id_formation'])) {
    $id_formation = $_GET['id_formation'];

    // Vérifier si l'ID de la formation est vide
    if ($id_formation == "") {
        echo json_encode(array('error' => 'Champs vides'));
        return;
    }

    try {
        // Appel la fonction getFormationDetails pour obtenir les détails de la formation
        $data = getFormationDetails($id_formation);

        header('Content-Type: application/json');
        echo json_encode($data);

    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(array('error' => 'Erreur lors de la récupération des détails de la formation'));
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(array('error' => 'ID de formation non fourni'));
}
?>
