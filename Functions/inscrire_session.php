<?php
include 'Bd_connect.php';

if (isset($_POST['idSession'])) {
    $idSession = $_POST['idSession'];
    $idUtilisateur = $_SESSION['id_utilisateur']; // Assure-toi d'avoir une session active

    include('functions.php'); // Inclure le fichier avec la fonction inscrireSessions

    // Appel de la fonction pour inscrire l'utilisateur à la session
    inscrireSessions($idSession, $idUtilisateur);

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'ID de session manquant']);
}

?>
