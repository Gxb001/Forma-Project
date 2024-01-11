<?php
include 'functions.php';
if (isset($_GET['id_session'])) {
    $id_session = $_GET['id_session'];

    // Vérifier si l'ID de la formation est vide
    if ($id_session == "") {
        echo json_encode(array('error' => 'Champs vides'));
        return;
    }

    try {
        // Appel la fonction getFormationDetails pour obtenir les détails de la formation
        // Récupérer les détails de la session
        $data = getSessionDetails($id_session);

        $intervenants = getIntervenantsSession($id_session);

        $data['intervenants'] = $intervenants;


        header('Content-Type: application/json');
        echo json_encode($data);

    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(array('error' => 'Erreur lors de la récupération des détails de la session'));
    }
} else {
    // L'ID de la formation n'est pas fourni dans la requête
    header('Content-Type: application/json');
    echo json_encode(array('error' => 'ID de session non fourni'));
}
?>
