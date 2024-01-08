<?php
include('functions.php');
session_start();

if (isset($_POST['idSession'], $_POST['idUtilisateur'])) {
    $idSession = $_POST['idSession'];
    $idUtilisateur = $_POST['idUtilisateur'];


    echo "ID de session : $idSession, ID utilisateur : $idUtilisateur";


    //Vérifier si l'utilisateur peut s'inscrire
    if (verif_admin($idUtilisateur)) {
        echo json_encode(['adm-att' => 'Vous êtes administrateur, vous ne pouvez pas vous inscrire à une session']);
    } elseif (!verif_inscription_count($idUtilisateur)) {
        echo json_encode(['ltm-att' => 'Vous avez atteint le nombre maximum d\'inscriptions pour cette année (3)']);
    } elseif (!verif_domaine_inscription($idUtilisateur, $idSession)) {
        echo json_encode(['dm-att' => 'Vous avez déjà deux inscriptions dans ce domaine']);
    } elseif (inscriptionExisteDeja($idSession, $idUtilisateur)) {
        $etat = inscriptionExisteDeja($idSession, $idUtilisateur);
        if ($etat == "Acceptée" || $etat == "Refusée") {
            echo json_encode(['ttr-cra' => 'Votre demande d\'inscription a déjà été traitée']);
        } else {
            echo json_encode(['ttr-crs' => 'Votre demande d\'inscription est déjà en cours de traitement']);
        }

    } else {
        //Vérifier si la session a encore des places disponibles
        $session = getSessionDetails($idSession);

        if ($session && $session['nb_participant'] < $session['nb_max']) {
            //Ajouter l'inscription
            inscrireSessions($idSession, $idUtilisateur);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['ss-cmpt' => 'La session est complète']);
        }
    }
} else {
    echo json_encode(['error' => 'ID de session ou ID d\'utilisateur manquant']);
}
?>
