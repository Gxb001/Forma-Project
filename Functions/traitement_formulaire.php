<?php
// Inclure le fichier de connexion à la base de données
include("Bd_connect.php");

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $idFormation = $_POST["formation"];
    $dateDebut = $_POST["date_debut"];
    $lieu = $_POST["lieu"];
    $intervenant = $_POST["intervenant"];

    // Préparer la requête d'insertion
    $query = "INSERT INTO sessionsformation (ID_Formation, DateDebut, Lieu, Intervenant) VALUES (?, ?, ?, ?)";
    $stmt = $connexion->prepare($query);

    // Exécution de la requête avec les valeurs liées
    $stmt->execute([$idFormation, $dateDebut, $lieu, $intervenant]);

   
}
?>
