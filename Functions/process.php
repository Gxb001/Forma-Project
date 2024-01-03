<?php
if (isset($_POST['creer_formation'])) {
    // Récupérer les données du formulaire
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $domaine = $_POST['domaine'];
    $cout = $_POST['cout'];
    $places_disponibles = $_POST['places_disponibles'];

    // Requête d'insertion dans la table Formations
    $requete = $connexion->prepare("INSERT INTO Formations (Titre, Description, Domaine, Cout, PlacesDisponibles) VALUES (?, ?, ?, ?, ?)");
    $requete->execute([$titre, $description, $domaine, $cout, $places_disponibles]);

    // Redirection vers la page d'administration
    header("Location: ../formations.html.php");
    exit();
}

if (isset($_POST['modifier_formation'])) {
    $formation_id = $_POST['formation_id'];
    $titre = $_POST['titre'];
    // Récupérez d'autres champs de formulaire ici...

    // Requête de mise à jour dans la table Formations
    $requete_modification = $connexion->prepare("UPDATE Formations SET Titre = ? WHERE ID_Formation = ?");
    $requete_modification->execute([$titre, $formation_id]);

    // Redirection vers la page d'administration
    header("Location: ../formations.html.php");
    exit();
}

// Traitement de la suppression d'une formation
if (isset($_POST['supprimer_formation'])) {
    $formation_id = $_POST['formation_id'];

    // Requête de suppression dans la table Formations
    $requete_suppression = $connexion->prepare("DELETE FROM Formations WHERE ID_Formation = ?");
    $requete_suppression->execute([$formation_id]);

    // Redirection vers la page d'administration
    header("Location: ../formations.html.php");
    exit();
}


?>