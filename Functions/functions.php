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
//fonction qui crée un utilisateur
function creation_user($nom, $prenom, $email, $mdp, $status, $association)
{
    global $connexion;
    $mdp = password_hash($mdp, PASSWORD_DEFAULT);
    $sql = "INSERT INTO utilisateurs (nom, prenom, email, mdp, statut, association) VALUES ('$nom', '$prenom', '$email', '$mdp', '$status', '$association')";

    try {
        $connexion->exec($sql);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    } finally {
        $connexion = null;
    }
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
//fonction qui crée une formation
function creation_formation($libelle, $description, $cout, $contenu, $objectif, $nb_place, $id_domaine)
{
    global $connexion;
    $sql = "INSERT INTO formations (libelle_formation, description_formation, coût, contenu, objectif, nb_place, id_domaine) VALUES ('$libelle', '$description', '$cout', '$contenu', '$objectif', '$nb_place', '$id_domaine')";

    try {
        $connexion->exec($sql);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    } finally {
        $connexion = null;
    }
}


/**
 * @return mixed
 */
//fonction qui récupère les formations
function getFormations()
{
    global $connexion;
    $sql = "SELECT * FROM formations;";

    try {
        $result = $connexion->query($sql);
        return $result;
    } catch (PDOException $e) {
        return false;
    } finally {
        $connexion = null;
    }
}

/**
 * @param $idFormation
 * @return false|PDOStatement
 */
//fonction qui récupère les sessions  d'une formation
function getSessionsFormation($idFormation)
{
    global $connexion;

    try {
        $sql = "SELECT * FROM sessionsformations WHERE id_formation = :idFormation";
        $stmt = $connexion->prepare($sql);
        $stmt->bindParam(':idFormation', $idFormation, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt;
    } catch (PDOException $e) {
        return false;
    }
}


/**
 * @param $id_session
 * @param $date_session
 * @param $heure_debut
 * @param $heure_fin
 * @param $lieux
 * @param $nb_participant
 * @param $date_limite
 * @param $id_formation
 * @return void
 */
// Fonction pour créer une nouvelle session
function creerNouvelleSession($id_session, $date_session, $heure_debut, $heure_fin, $lieux, $nb_participant, $date_limite, $id_formation)
{
    global $connexion;

    try {
        // Préparer la requête d'insertion
        $query = "INSERT INTO sessionsformations (id_session, date_session, heure_debut, heure_fin, lieux, nb_participant, date_limite, id_formation) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $connexion->prepare($query);

        // Exécution de la requête avec les valeurs liées
        $stmt->execute([$id_session, $date_session, $heure_debut, $heure_fin, $lieux, $nb_participant, $date_limite, $id_formation]);

        echo "Nouvelle session créée avec succès.";
    } catch (PDOException $e) {
        echo "Erreur PDO : " . $e->getMessage();
    }
}

//creation_user("Ferrer", "Gabriel", "gabfer258@gmail.com", "Azerty31", "B", "Venez comme vous-etes");
//creation_user("Doumbia", "Bamody", "d.bamody28@gmail.com", "Azerty31", "A", "Club de ping pong");

//creation_formation("Formation D\'escrime", "Cours d\'escrime en groupe de 10", 30, "10 cours sur 5 semaines", "Apprendre les base de l\'escrime", 15, 2);
//creation_formation("Formation Powerpoint", "Decouverte du logiciel", 30, "1 cours ", "Apprendre powerpoint", 10, 1);