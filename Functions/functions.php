<?php
function obtenirConnexion()
{
    $servername = 'localhost';
    $username = 'root';
    $password = '';
    $dbname = 'forma';

    try {
        $connexion = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $connexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        return $connexion;
    } catch (PDOException $e) {
        echo 'Erreur de connexion : ' . $e->getMessage();
        return null;
    }
}

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
    $connexion = obtenirConnexion();
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
    $connexion = obtenirConnexion();
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
    $connexion = obtenirConnexion();
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
    $connexion = obtenirConnexion();

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
    $connexion = obtenirConnexion();

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
    $connexion = obtenirConnexion();

    try {
        //Sélectionne les demandes d'inscription en cours
        $sql = "SELECT * FROM inscription WHERE etat = 'En Cours'";
        $stmt = $connexion->query($sql);

        //Retourne les résultats en tant qu'array associatif
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erreur de requête : " . $e->getMessage();
        return []; //Retourne un tableau vide en cas d'erreur
    }
}

/**
 * @param $id_session
 * @param $id_utilisateur
 * @return void
 */
function inscrireSessions($id_session, $id_utilisateur)
{
    $connexion = obtenirConnexion();

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
    $connexion = obtenirConnexion();

    try {
        $query = "SELECT etat FROM inscription WHERE id_session = ? AND id_utilisateur = ?";
        $stmt = $connexion->prepare($query);
        $stmt->execute([$idSession, $idUtilisateur]);

        $result = $stmt->fetchColumn();

        // Si une inscription existe, retournez son état, sinon retournez false
        return ($result !== false) ? $result : false;
    } catch (PDOException $e) {
        // Loguer l'erreur dans un fichier de logs par exemple
        error_log("Erreur PDO lors de la récupération de l'état de l'inscription : " . $e->getMessage());
        return false;
    }
}

function sessionsDejaAccepte($idSession, $idUtilisateur)
{
    $etat = inscriptionExisteDeja($idSession, $idUtilisateur);
    if ($etat == 'Acceptée') {
        return true;
    } else {
        return false;
    }
}


/**
 * @param $idDomaine
 * @return false|mixed
 */
function getDomaine($idDomaine)
{
    $connexion = obtenirConnexion();
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
    $connexion = obtenirConnexion();

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
    $connexion = obtenirConnexion();

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

/**
 * @param $id_utilisateur
 * @param $id_session
 * @return false|mixed
 */
function verif_inscription_count($id_utilisateur)
{
    $connexion = obtenirConnexion();
    $sql = "SELECT COUNT(*) FROM inscription WHERE id_utilisateur = '$id_utilisateur' AND YEAR(date_inscription) = YEAR(CURDATE()) AND etat = 'Acceptée'";

    try {
        $result = $connexion->query($sql);
        $count = $result->fetchColumn();

        // Vérifier si le nombre d'inscriptions est inférieur à 3
        return $count < 3;
    } catch (PDOException $e) {
        return false;
    } finally {
        $connexion = null;
    }
}

/**
 * @param $id_utilisateur
 * @param $id_session
 * @return bool
 */
function verif_domaine_inscription($id_utilisateur, $id_session)
{
    $connexion = obtenirConnexion();

    try {
        // Sélectionner l'id_formation de la session actuelle
        $sql_formation = "SELECT id_formation FROM sessionformations WHERE id_session = '$id_session'";
        $result_formation = $connexion->query($sql_formation);
        $id_formation = $result_formation->fetchColumn();

        // Sélectionner l'id_domaine de la formation associée à la session actuelle
        $sql_domaine = "SELECT id_domaine FROM formations WHERE id_formation = '$id_formation'";
        $result_domaine = $connexion->query($sql_domaine);
        $id_domaine = $result_domaine->fetchColumn();

        // Vérifier si l'utilisateur a déjà deux inscriptions dans le même domaine
        $sql = "SELECT COUNT(*) 
                FROM inscription
                INNER JOIN sessionformations ON inscription.id_session = sessionformations.id_session
                INNER JOIN formations ON sessionformations.id_formation = formations.id_formation
                WHERE inscription.id_utilisateur = '$id_utilisateur'
                AND formations.id_domaine = '$id_domaine'";

        $result = $connexion->query($sql);
        $count = $result->fetchColumn();

        // Retourner vrai si l'utilisateur peut s'inscrire (moins de deux inscriptions dans le même domaine)
        return $count < 2;
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * @param $idUtilisateur
 * @return bool
 */
function verif_admin($idUtilisateur)
{
    $connexion = obtenirConnexion();
    $sql = "SELECT statut FROM utilisateurs WHERE id_utilisateur = '$idUtilisateur'";

    try {
        $result = $connexion->query($sql);
        $statut = $result->fetchColumn();

        // Vérifier si le nombre d'inscriptions est inférieur à 3
        return $statut == 'A';
    } catch (PDOException $e) {
        return false;
    } finally {
        $connexion = null;
    }
}

function getUtilisateurDetails($idUtilisateur)
{
    $connexion = obtenirConnexion();

    try {
        $query = "SELECT nom, prenom, email, association FROM utilisateurs WHERE id_utilisateur = ?";
        $stmt = $connexion->prepare($query);
        $stmt->execute([$idUtilisateur]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Loguer l'erreur dans un fichier de logs par exemple
        error_log("Erreur PDO lors de la récupération des détails de l'utilisateur : " . $e->getMessage());
        return false;
    }
}

/**
 * @param $idSession
 * @return false|mixed
 */
function getFormationDetailsSession($idSession)
{
    $connexion = obtenirConnexion();

    try {
        $query = "SELECT * FROM formations WHERE id_formation = (SELECT id_formation FROM sessionformations WHERE id_session = ?)";
        $stmt = $connexion->prepare($query);
        $stmt->execute([$idSession]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Loguer l'erreur dans un fichier de logs par exemple
        error_log("Erreur PDO lors de la récupération des détails de la formation : " . $e->getMessage());
        return false;
    }

}

function getUsersFromSessions($idSession)
{
    $connexion = obtenirConnexion();

    try {
        $query = "SELECT * FROM utilisateurs WHERE id_utilisateur IN (SELECT id_utilisateur FROM inscription WHERE id_session = ?)";
        $stmt = $connexion->prepare($query);
        $stmt->execute([$idSession]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Loguer l'erreur dans un fichier de logs par exemple
        error_log("Erreur PDO lors de la récupération des détails de la formation : " . $e->getMessage());
        return false;
    }
}

/**
 * @param $idSession
 * @param $idUtilisateur
 * @return mixed|string
 */
function getStatutUsers($idSession, $idUtilisateur)
{
    $connexion = obtenirConnexion();

    try {
        $sqlInscription = "SELECT etat FROM inscription WHERE id_utilisateur = :idUtilisateur AND id_session = :idSession";
        $stmtInscription = $connexion->prepare($sqlInscription);
        $stmtInscription->bindParam(':idUtilisateur', $idUtilisateur);
        $stmtInscription->bindParam(':idSession', $idSession);
        $stmtInscription->execute();
        $etatInscription = $stmtInscription->fetchColumn();

        if ($etatInscription) {
            return $etatInscription;
        }

        //Récupération du nombre d'inscriptions en cours
        $sqlCountInscriptions = "SELECT COUNT(*) FROM inscription WHERE id_utilisateur = :idUtilisateur AND YEAR(date_inscription) = YEAR(CURDATE()) AND etat = 'Acceptée'";
        $stmtCountInscriptions = $connexion->prepare($sqlCountInscriptions);
        $stmtCountInscriptions->bindParam(':idUtilisateur', $idUtilisateur);
        $stmtCountInscriptions->execute();
        $count = $stmtCountInscriptions->fetchColumn();

        //Récupération de l'id_formation
        $sqlFormation = "SELECT id_formation FROM sessionformations WHERE id_session = :idSession";
        $stmtFormation = $connexion->prepare($sqlFormation);
        $stmtFormation->bindParam(':idSession', $idSession);
        $stmtFormation->execute();
        $idFormation = $stmtFormation->fetchColumn();

        //Récupération de l'id_domaine
        $sqlDomaine = "SELECT id_domaine FROM formations WHERE id_formation = :idFormation";
        $stmtDomaine = $connexion->prepare($sqlDomaine);
        $stmtDomaine->bindParam(':idFormation', $idFormation);
        $stmtDomaine->execute();
        $idDomaine = $stmtDomaine->fetchColumn();

        //Vérification du nombre d'inscriptions dans le même domaine
        $sqlCountDomaine = "SELECT COUNT(*) FROM inscription
                            INNER JOIN sessionformations ON inscription.id_session = sessionformations.id_session
                            INNER JOIN formations ON sessionformations.id_formation = formations.id_formation
                            WHERE inscription.id_utilisateur = :idUtilisateur
                            AND formations.id_domaine = :idDomaine";
        $stmtCountDomaine = $connexion->prepare($sqlCountDomaine);
        $stmtCountDomaine->bindParam(':idUtilisateur', $idUtilisateur);
        $stmtCountDomaine->bindParam(':idDomaine', $idDomaine);
        $stmtCountDomaine->execute();
        $countDomaine = $stmtCountDomaine->fetchColumn();

        //Récupération du statut de l'utilisateur
        $sqlStatut = "SELECT statut FROM utilisateurs WHERE id_utilisateur = :idUtilisateur";
        $stmtStatut = $connexion->prepare($sqlStatut);
        $stmtStatut->bindParam(':idUtilisateur', $idUtilisateur);
        $stmtStatut->execute();
        $statut = $stmtStatut->fetchColumn();

        //Récupération du nombre de participants et de places disponibles pour la session
        $sqlPlacesSession = "SELECT nb_participant, nb_place FROM sessionformations WHERE id_session = :idSession";
        $stmtPlacesSession = $connexion->prepare($sqlPlacesSession);
        $stmtPlacesSession->bindParam(':idSession', $idSession);
        $stmtPlacesSession->execute();
        $nbParticipant = $stmtPlacesSession->fetchColumn();
        $nbPlace = $stmtPlacesSession->fetchColumn();

        //Vérification des conditions pour le statut
        if ($count < 3 && $countDomaine < 2 && $statut != 'A' && $nbParticipant < $nbPlace) {
            return true;
        } else {
            return false;
        }
    } catch (PDOException $e) {
        error_log("Erreur PDO dans getStatutUsers : " . $e->getMessage());
        return 'Erreur';
    }
}

/**
 * @param $date
 * @return string
 * @throws Exception
 */
function formatDate($date)
{
    $dateObj = new DateTime($date);
    $moisEnFrancais = [
        'janvier', 'février', 'mars', 'avril', 'mai', 'juin',
        'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'
    ];
    $mois = $moisEnFrancais[intval($dateObj->format('n')) - 1];
    $dateFormatee = $dateObj->format('j ') . $mois . $dateObj->format(' Y');
    return ucfirst($dateFormatee); //majuscule
}

/**
 * @param $idFormation
 * @return false|PDOStatement
 */
function getAllUsersFormation($idFormation)
{
    $connexion = obtenirConnexion();
    $sql = "SELECT u.id_utilisateur, u.nom, u.prenom, u.email, u.association, u.tel, u.adresse, u.ville, u.cp, s.id_formation, f.libelle_formation 
            FROM utilisateurs u
            JOIN inscription i ON u.id_utilisateur = i.id_utilisateur
            JOIN sessionformations s ON i.id_session = s.id_session
            JOIN formations f ON s.id_formation = f.id_formation
            WHERE s.id_formation = '$idFormation' AND i.etat = 'Acceptée'";
    try {
        $result = $connexion->query($sql);
        return $result;
    } catch (PDOException $e) {
        return false;
    } finally {
        $connexion = null;
    }
}





//creation_user("Ferrer", "Gabriel", "gabfer258@gmail.com", "Azerty31", "B", "Venez comme vous-etes");
//creation_user("Doumbia", "Bamody", "d.bamody28@gmail.com", "Azerty31", "A", "Club de ping pong");

//creation_formation("Formation D\'escrime", "Cours d\'escrime en groupe de 10", 30, "10 cours sur 5 semaines", "Apprendre les base de l\'escrime", 15, 2);
//creation_formation("Formation Powerpoint", "Decouverte du logiciel", 30, "1 cours ", "Apprendre powerpoint", 10, 1);