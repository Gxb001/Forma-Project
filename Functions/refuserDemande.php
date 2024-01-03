<?php

include "Bd_connect.php";

if (isset($_POST['idInscription'])) {
    $idInscription = $_POST['idInscription'];

    try {
        // Mettre à jour l'état de l'inscription à "Refusé" dans la table inscription
        $sqlUpdate = "UPDATE inscription SET etat = 'Refusé' WHERE id_inscription = :idInscription";
        $stmtUpdate = $connexion->prepare($sqlUpdate);
        $stmtUpdate->bindParam(':idInscription', $idInscription, PDO::PARAM_INT);
        $stmtUpdate->execute();

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Erreur lors du refus de la demande']);
    }
} else {
    echo json_encode(['error' => 'ID d\'inscription manquant']);
}

?>
