<?php

include 'functions.php';
$connexion = obtenirConnexion();
session_start();

if (isset($_POST['idSession'], $_POST['idUtilisateur'])) {
    $idSession = $_POST['idSession'];
    $idUtilisateur = $_POST['idUtilisateur'];

    try {
        //Mettre à jour l'état de l'inscription à "Accepté" dans la table inscription
        $sqlUpdate = "UPDATE inscription SET etat = 'Acceptée' WHERE id_session = :idSession AND id_utilisateur = :idUtilisateur";
        $stmtUpdate = $connexion->prepare($sqlUpdate);
        $stmtUpdate->bindParam(':idSession', $idSession, PDO::PARAM_INT);
        $stmtUpdate->bindParam(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
        $stmtUpdate->execute();

        //Vérifier si l'utilisateur n'a pas encore été compté comme participant pour cette session
        $sqlCheckParticipant = "SELECT COUNT(*) FROM inscription WHERE id_utilisateur = :idUtilisateur AND id_session = :idSession";
        $stmtCheckParticipant = $connexion->prepare($sqlCheckParticipant);
        $stmtCheckParticipant->bindParam(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
        $stmtCheckParticipant->bindParam(':idSession', $idSession, PDO::PARAM_INT);
        $stmtCheckParticipant->execute();
        $countParticipant = $stmtCheckParticipant->fetchColumn();

        if ($countParticipant == 1) {
            // Mettre à jour le nombre de participants dans la table formations
            $sqlIncrementFormation = "UPDATE formations SET nb_participants = nb_participants + 1 WHERE id_formation IN (SELECT id_formation FROM sessionformations WHERE id_session = :idSession)";
            $stmtIncrementFormation = $connexion->prepare($sqlIncrementFormation);
            $stmtIncrementFormation->bindParam(':idSession', $idSession, PDO::PARAM_INT);
            $stmtIncrementFormation->execute();
        }

        //Mettre à jour le nombre de participants dans la table sessionsformations
        $sqlIncrementSession = "UPDATE sessionformations SET nb_participant = nb_participant + 1 WHERE id_session = :idSession";
        $stmtIncrementSession = $connexion->prepare($sqlIncrementSession);
        $stmtIncrementSession->bindParam(':idSession', $idSession, PDO::PARAM_INT);
        $stmtIncrementSession->execute();

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        //Loguer l'erreur dans un fichier de logs par exemple
        error_log("Erreur PDO lors de l'acceptation de la demande : " . $e->getMessage());
        echo json_encode(['error' => 'Erreur lors de l\'acceptation de la demande']);
    }
} else {
    echo json_encode(['error' => 'ID de session ou ID d\'utilisateur manquant']);
}
?>
