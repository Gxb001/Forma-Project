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
        $sql = "SELECT * FROM sessionformations WHERE id_formation = :idFormation";
        $stmt = $connexion->prepare($sql);

        if (!$stmt) {
            // Gestion de l'erreur de préparation
            throw new PDOException('Erreur de préparation de la requête SQL.');
        }

        $stmt->bindParam(':idFormation', $idFormation, PDO::PARAM_INT);

        if (!$stmt->execute()) {
            // Gestion de l'erreur d'exécution de la requête
            throw new PDOException('Erreur d\'exécution de la requête SQL.');
        }

        return $stmt;
    } catch (PDOException $e) {
        // Affiche l'erreur dans la console du navigateur
        echo json_encode(['error' => $e->getMessage()]);
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

/**
 * @return array|false
 */
function getDemandesInscriptionsEnCours()
{
    global $connexion; // Assure-toi d'avoir une connexion à la base de données

    try {
        // Sélectionne les demandes d'inscription en cours
        $sql = "SELECT * FROM inscription WHERE etat = 'En Cours'";
        $stmt = $connexion->query($sql);

        // Retourne les résultats en tant qu'array associatif
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Gère les erreurs de requête SQL
        echo "Erreur de requête : " . $e->getMessage();
        return []; // Retourne un tableau vide en cas d'erreur
    }
}

/**
 * @param $id_session
 * @param $id_utilisateur
 * @return void
 */
function inscrireSessions($id_session, $id_utilisateur)
{
    global $connexion;

    try {
        $id_session = intval($id_session);
        $id_utilisateur = intval($id_utilisateur);

        $date_inscription = date('Y-m-d');

        $etat = 'En Cours';

        $query = "INSERT INTO inscription (id_session, id_utilisateur, date_inscription, etat) VALUES (?, ?, ?, ?)";
        $stmt = $connexion->prepare($query);

        $stmt->execute([$id_session, $id_utilisateur, $date_inscription, $etat]);

        echo "Inscription réussie avec succès.";
    } catch (PDOException $e) {
        // Loguer l'erreur dans un fichier de logs par exemple
        error_log("Erreur PDO : " . $e->getMessage());
        echo "Une erreur s'est produite lors de l'inscription.";
    }
}

/**
 * @param $idSession
 * @param $idUtilisateur
 * @return bool
 */
function inscriptionExisteDeja($idSession, $idUtilisateur)
{
    global $connexion;

    try {
        $query = "SELECT COUNT(*) FROM inscription WHERE id_session = ? AND id_utilisateur = ?";
        $stmt = $connexion->prepare($query);
        $stmt->execute([$idSession, $idUtilisateur]);

        $count = $stmt->fetchColumn();

        return ($count > 0);
    } catch (PDOException $e) {
        // Loguer l'erreur dans un fichier de logs par exemple
        error_log("Erreur PDO lors de la vérification de l'existence de l'inscription : " . $e->getMessage());
        return false;
    }
}

/**
 * @param $idDomaine
 * @return false|mixed
 */
function getDomaine($idDomaine)
{
    global $connexion;
    $query = "SELECT libelle_domaine FROM domaines WHERE id_domaine = ?";
    try {
        $stmt = $connexion->prepare($query);
        $stmt->execute([$idDomaine]);

        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        echo "Erreur PDO : " . $e->getMessage();
        return false;
    }
}

/**
 * @param $idSession
 * @param $idUtilisateur
 * @return false|mixed
 */
function getStatusSessionsUser($idSession, $idUtilisateur)
{
    global $connexion;

    try {
        $query = "SELECT etat FROM inscription WHERE id_session = ? AND id_utilisateur = ?";
        $stmt = $connexion->prepare($query);
        $stmt->execute([$idSession, $idUtilisateur]);

        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        // Loguer l'erreur dans un fichier de logs par exemple
        error_log("Erreur PDO lors de la récupération du statut de l'inscription : " . $e->getMessage());
        return false;
    }
}

/**
 * Récupère les détails d'une session à partir de son ID.
 *
 * @param int $idSession L'ID de la session à récupérer.
 * @return array|false Les détails de la session ou false si la session n'est pas trouvée.
 */
function getSessionDetails($idSession)
{
    global $connexion;

    try {
        $query = "SELECT * FROM sessionformations WHERE id_session = ?";
        $stmt = $connexion->prepare($query);
        $stmt->execute([$idSession]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Loguer l'erreur dans un fichier de logs par exemple
        error_log("Erreur PDO lors de la récupération des détails de la session : " . $e->getMessage());
        return false;
    }
}







//creation_user("Ferrer", "Gabriel", "gabfer258@gmail.com", "Azerty31", "B", "Venez comme vous-etes");
//creation_user("Doumbia", "Bamody", "d.bamody28@gmail.com", "Azerty31", "A", "Club de ping pong");

//creation_formation("Formation D\'escrime", "Cours d\'escrime en groupe de 10", 30, "10 cours sur 5 semaines", "Apprendre les base de l\'escrime", 15, 2);
//creation_formation("Formation Powerpoint", "Decouverte du logiciel", 30, "1 cours ", "Apprendre powerpoint", 10, 1);