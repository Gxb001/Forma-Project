<?php
include 'Bd_connect.php';
include "functions.php";

session_start();

if (isset($_POST['idSession'], $_POST['idUtilisateur'])) {
    $idSession = $_POST['idSession'];
    $idUtilisateur = $_POST['idUtilisateur'];

    echo "ID de session : $idSession, ID utilisateur : $idUtilisateur";

    include('functions.php');

    // Vérifier si l'inscription existe déjà
    if (inscriptionExisteDeja($idSession, $idUtilisateur)) {
        echo json_encode(['error' => 'Inscription déjà existante']);
    } else {
        // Vérifier si la session a encore des places disponibles
        $session = getSessionDetails($idSession);

        if ($session && $session['nb_participant'] < $session['nb_max']) {
            // Ajouter l'inscription
            inscrireSessions($idSession, $idUtilisateur);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => 'La session est complète']);
        }
    }
} else {
    echo json_encode(['error' => 'ID de session ou ID d\'utilisateur manquant']);
}
?>
