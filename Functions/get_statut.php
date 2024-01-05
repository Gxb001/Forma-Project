<?php
include 'functions.php';

if (isset($_GET['idSession']) && isset($_GET['idUtilisateur'])) {

    $idSession = $_GET['idSession'];
    $idUtilisateur = $_GET['idUtilisateur'];

    $statut = getStatutUsers($idSession, $idUtilisateur);

    switch ($statut) {
        case 'En cours':
        case 'Acceptée':
        case 'Refusée':
            echo $statut;
            break;
        case true:
            echo 'eligible';
            break;
        case false:
            echo 'non-eligible';
            break;
        default:
            echo 'error';
            break;
    }
} else {
    echo 'error';
}
?>
