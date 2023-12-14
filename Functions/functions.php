<?php
function CheckPasswordHash($password, $hash)
{
    if (password_verify($password, $hash)) {
        return true;
    } else {
        return false;
    }
}


function creerNouvelleFormation($titre, $description, $domaine, $cout, $placesDisponibles) {
    include("Bd_connect.php");

    try {
        // Préparer la requête d'insertion
        $query = "INSERT INTO formations (Titre, Description, Domaine, Cout, PlacesDisponibles) VALUES (?, ?, ?, ?, ?)";
        $stmt = $connexion->prepare($query);

        // Exécution de la requête avec les valeurs liées
        $stmt->execute([$titre, $description, $domaine, $cout, $placesDisponibles]);

        echo "Nouvelle formation créée avec succès.";
    } catch (PDOException $e) {
        echo "Erreur PDO : " . $e->getMessage();
    } finally {
        // Fermer la connexion PDO
        $connexion = null;
    }
}

// Exemple d'utilisation de la fonction
creerNouvelleFormation("Formation PowerPoint", "Description de la formation CSS", "Informatique", 100, 20);

// Fonction pour créer une nouvelle session
function creerNouvelleSession($idFormation, $dateDebut, $lieu, $intervenant) {
    global $connexion;

    try {
        // Préparer la requête d'insertion
        $query = "INSERT INTO sessions (ID_Formation, DateDebut, Lieu, Intervenant) VALUES (?, ?, ?, ?)";
        $stmt = $connexion->prepare($query);

        // Exécution de la requête avec les valeurs liées
        $stmt->execute([$idFormation, $dateDebut, $lieu, $intervenant]);

        echo "Nouvelle session créée avec succès.";
    } catch (PDOException $e) {
        echo "Erreur PDO : " . $e->getMessage();
    }
}