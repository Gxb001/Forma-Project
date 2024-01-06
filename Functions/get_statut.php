<?php
include 'functions.php';

if (isset($_GET['idSession']) && isset($_GET['idUtilisateur'])) {

    $idSession = $_GET['idSession'];
    $idUtilisateur = $_GET['idUtilisateur'];

    if (verif_admin($idUtilisateur)) {
        echo 'non-eligible';
        exit();
    } else {
        $statut = getStatutUsers($idSession, $idUtilisateur);
    }

    switch ($statut) {
        case 'En Cours':
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
    exit();
} else {
    echo 'error';
}
?>
