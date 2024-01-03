<?php

include 'Bd_connect.php';

if (isset($_POST['idInscription'])) {
    $idInscription = $_POST['idInscription'];

    try {
        // Mettre à jour l'état de l'inscription à "Accepté" dans la table inscription
        $sqlUpdate = "UPDATE inscription SET etat = 'Accepté' WHERE id_inscription = :idInscription";
        $stmtUpdate = $connexion->prepare($sqlUpdate);
        $stmtUpdate->bindParam(':idInscription', $idInscription, PDO::PARAM_INT);
        $stmtUpdate->execute();

        // Mettre à jour le nombre de participants dans la table sessionsformations
        // (Assure-toi d'avoir les tables et les colonnes correctes dans ta base de données)
        $sqlIncrement = "UPDATE sessionsformations SET nb_participant = nb_participant + 1 WHERE id_session = (
                            SELECT id_session FROM inscription WHERE id_inscription = :idInscription
                        )";
        $stmtIncrement = $connexion->prepare($sqlIncrement);
        $stmtIncrement->bindParam(':idInscription', $idInscription, PDO::PARAM_INT);
        $stmtIncrement->execute();

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Erreur lors de l\'acceptation de la demande']);
    }
} else {
    echo json_encode(['error' => 'ID d\'inscription manquant']);
}

?>
