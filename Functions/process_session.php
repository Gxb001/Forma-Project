<?php
// process_session.php

// Vérifier si le formulaire de session a été soumis
if (isset($_POST['submit_session'])) {
    // Inclure le fichier de connexion à la base de données
    include("Bd_connect.php");

    // Récupérer les données du formulaire
    $idFormation = $_POST['formation'];
    $dateDebut = $_POST['date_debut'];
    $lieu = $_POST['lieu'];
    $intervenant = $_POST['intervenant'];

    // Appeler la fonction pour créer une nouvelle session
    creerNouvelleSession($idFormation, $dateDebut, $lieu, $intervenant);

    // Fermer la connexion PDO (si nécessaire)
    $connexion = null;
}

// Redirection vers la page d'origine ou autre après le traitement
// header("Location: page_d_origine.php");
exit();
?>
