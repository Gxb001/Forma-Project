<?php

include 'functions.php';
$connexion = obtenirConnexion();

if (isset($_POST['idSession'], $_POST['idUtilisateur'])) {
    $idSession = $_POST['idSession'];
    $idUtilisateur = $_POST['idUtilisateur'];

    try {
        $connexion->beginTransaction();

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
            //Vérifier si c'est la première fois que l'utilisateur s'inscrit à une session de cette formation
            $sqlCheckFirstTime = "SELECT COUNT(*) FROM inscription AS i
                          JOIN sessionformations AS sf ON i.id_session = sf.id_session
                          WHERE i.id_utilisateur = :idUtilisateur AND sf.id_formation = (SELECT sf2.id_formation FROM sessionformations AS sf2 WHERE sf2.id_session = :idSession)";
            $stmtCheckFirstTime = $connexion->prepare($sqlCheckFirstTime);
            $stmtCheckFirstTime->bindParam(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
            $stmtCheckFirstTime->bindParam(':idSession', $idSession, PDO::PARAM_INT);
            $stmtCheckFirstTime->execute();
            $countFirstTime = $stmtCheckFirstTime->fetchColumn();

            if ($countFirstTime == 1) {
                $sqlIncrementFormation = "UPDATE formations SET nb_participants = nb_participants + 1 WHERE id_formation IN (SELECT sf.id_formation FROM sessionformations AS sf WHERE sf.id_session = :idSession)";
                $stmtIncrementFormation = $connexion->prepare($sqlIncrementFormation);
                $stmtIncrementFormation->bindParam(':idSession', $idSession, PDO::PARAM_INT);
                $stmtIncrementFormation->execute();
            }
        }

        $sqlIncrementSession = "UPDATE sessionformations SET nb_participant = nb_participant + 1 WHERE id_session = :idSession";
        $stmtIncrementSession = $connexion->prepare($sqlIncrementSession);
        $stmtIncrementSession->bindParam(':idSession', $idSession, PDO::PARAM_INT);
        $stmtIncrementSession->execute();

        $connexion->commit();

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        //Loguer l'erreur
        $connexion->rollBack();
        error_log("Erreur PDO lors de l'acceptation de la demande : " . $e->getMessage());
        echo json_encode(['error' => 'Erreur lors de l\'acceptation de la demande' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'ID de session ou ID d\'utilisateur manquant']);
}
?>
