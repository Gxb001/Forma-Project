<?php

include("Bd_connect.php");
/**
 * @param $nom
 * @param $prenom
 * @param $email
 * @param $mdp
 * @param $status
 * @param $association
 * @return void
 */
function creation_user($nom, $prenom, $email, $mdp, $status, $association)
{
    global $connexion;
    $mdp = password_hash($mdp, PASSWORD_DEFAULT);
    $sql = "INSERT INTO utilisateurs (nom, prenom, email, mdp, statut, association) VALUES ('$nom', '$prenom', '$email', '$mdp', '$status', '$association')";
    $result = $connexion->query($sql);
    $result->closeCursor();
    $connexion = null;
}

/**
 * @param $nom
 * @param $description
 * @param $date_debut
 * @param $date_fin
 * @param $nb_participants
 * @param $nb_participants_max
 * @param $association
 * @return void
 */
function creation_formation($nom, $description, $date_debut, $date_fin, $nb_participants, $nb_participants_max, $association)
{
    global $connexion;
    $sql = "INSERT INTO formations (nom, description, date_debut, date_fin, nb_participants, nb_participants_max, association) VALUES ('$nom', '$description', '$date_debut', '$date_fin', '$nb_participants', '$nb_participants_max', '$association')";
    $result = $connexion->query($sql);
    $result->closeCursor();
    $connexion = null;
}

/**
 * @return mixed
 */
function getFormations()
{
    global $connexion;
    $sql = "SELECT * FROM formations";
    $result = $connexion->query($sql);
    $result->closeCursor();
    $connexion = null;
    return $result;

}

function creerNouvelleFormation($titre, $description, $domaine, $cout, $placesDisponibles)
{
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


// Fonction pour créer une nouvelle session
function creerNouvelleSession($idFormation, $dateDebut, $lieu, $intervenant)
{
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
//creation_user("Ferrer", "Gabriel", "gabfer258@gmail.com", "Azerty31", "B", "Venez comme vous-etes");
//creation_user("Doumbia", "Bamody", "d.bamody28@gmail.com", "Azerty31", "A", "Club de ping pong");

//creerNouvelleFormation("Formation PowerPoint", "Description de la formation CSS", "Informatique", 100, 20);