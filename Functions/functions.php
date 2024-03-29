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
 * @param $libelle
 * @param $cout
 * @param $contenu
 * @param $nb_place
 * @param $id_domaine
 * @return void
 */
//fonction qui crée une formation
function creation_formation($libelle, $cout, $contenu, $nb_place, $id_domaine)
{
    $connexion = obtenirConnexion();
    $sql = "INSERT INTO formations (libelle_formation, cout, contenu, nb_place, id_domaine, nb_participants) VALUES ('$libelle', '$cout', '$contenu', '$nb_place', '$id_domaine', 0)";

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
 * @param $date_session
 * @param $heure_debut
 * @param $heure_fin
 * @param $lieux
 * @param $date_limite
 * @param $id_formation
 * @param $nbmax
 * @return void
 */
// Fonction pour créer une nouvelle session
function creerNouvelleSession($date_session, $heure_debut, $heure_fin, $lieux, $date_limite, $id_formation, $nbmax)
{
    $connexion = obtenirConnexion();

    try {
        // Préparer la requête d'insertion
        $query = "INSERT INTO sessionformations (date_session, heure_debut, heure_fin, lieux, nb_participant, date_limite, id_formation, nb_max) VALUES (?, ?, ?, ?, 0, ?, ?, ?)";
        $stmt = $connexion->prepare($query);

        // Exécution de la requête avec les valeurs liées
        $stmt->execute([$date_session, $heure_debut, $heure_fin, $lieux, $date_limite, $id_formation, $nbmax]);

        // Récupérer l'ID de la session nouvellement créée
        $idNouvelleSession = $connexion->lastInsertId();

        // Retourner l'ID de la session
        return $idNouvelleSession;
    } catch (PDOException $e) {
        // Gérer les erreurs
        echo "Erreur PDO : " . $e->getMessage();
        return null; // Ou une autre valeur pour indiquer une erreur
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
    $sql = "SELECT COUNT(*) FROM inscription WHERE id_utilisateur = '$id_utilisateur' AND YEAR(date_inscription) = YEAR(CURDATE()) AND etat = 'Acceptée' or etat = 'En Cours'";

    try {
        $result = $connexion->query($sql);
        $count = $result->fetchColumn();

        //Vérifier si le nombre d'inscriptions est inférieur à 3
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

/**
 * @param $idSession
 * @return void
 * @throws Exception
 */
function supprimerSession($idSession)
{
    $connexion = obtenirConnexion();

    try {
        $sql = "DELETE FROM sessionformations WHERE id_session = :idSession";
        $stmt = $connexion->prepare($sql);
        $stmt->bindParam(':idSession', $idSession, PDO::PARAM_INT);
        $stmt->execute();
    } catch (PDOException $e) {
        throw new Exception("Erreur PDO lors de la suppression de la session : " . $e->getMessage());
    }
}

// Fonction pour supprimer une formation et toutes ses sessions
/**
 * @param $idFormation
 * @return void
 * @throws Exception
 */
function supprimerFormationEtSessions($idFormation)
{
    $connexion = obtenirConnexion();

    try {
        // Commencer une transaction
        $connexion->beginTransaction();

        // Récupérer les ID des sessions associées à la formation
        $sqlSessionsIds = "SELECT id_session FROM sessionformations WHERE id_formation = :idFormation";
        $stmtSessionsIds = $connexion->prepare($sqlSessionsIds);
        $stmtSessionsIds->bindParam(':idFormation', $idFormation, PDO::PARAM_INT);
        $stmtSessionsIds->execute();
        $sessionsIds = $stmtSessionsIds->fetchAll(PDO::FETCH_COLUMN);

        // Supprimer les intervenants associés à ces sessions dans la table 'intervient'
        foreach ($sessionsIds as $sessionId) {
            $sqlDeleteIntervient = "DELETE FROM intervient WHERE id_session = :idSession";
            $stmtDeleteIntervient = $connexion->prepare($sqlDeleteIntervient);
            $stmtDeleteIntervient->bindParam(':idSession', $sessionId, PDO::PARAM_INT);
            $stmtDeleteIntervient->execute();
            echo "Intervenants de la session $sessionId supprimés avec succès.";
        }

        // Supprimer toutes les sessions associées à la formation
        $sqlSessions = "DELETE FROM sessionformations WHERE id_formation = :idFormation";
        $stmtSessions = $connexion->prepare($sqlSessions);
        $stmtSessions->bindParam(':idFormation', $idFormation, PDO::PARAM_INT);
        $stmtSessions->execute();
        echo "Sessions supprimées avec succès.";

        // Supprimer la formation elle-même
        $sqlFormation = "DELETE FROM formations WHERE id_formation = :idFormation";
        $stmtFormation = $connexion->prepare($sqlFormation);
        $stmtFormation->bindParam(':idFormation', $idFormation, PDO::PARAM_INT);
        $stmtFormation->execute();
        echo "Formation supprimée avec succès.";

        // Valider la transaction
        $connexion->commit();
        echo "Transaction validée avec succès.";
    } catch (PDOException $e) {
        // En cas d'erreur, annuler la transaction
        $connexion->rollBack();
        echo "Erreur PDO lors de la suppression de la formation et de ses sessions : " . $e->getMessage();
        throw new Exception("Erreur PDO lors de la suppression de la formation et de ses sessions : " . $e->getMessage());
    }
}


/**
 * @param $idFormation
 * @return array
 */
function getSessionsByFormation($idFormation): array
{
    $connexion = obtenirConnexion();

    try {
        $query = "SELECT * FROM sessionformations WHERE id_formation = :idFormation";
        $stmt = $connexion->prepare($query);
        $stmt->bindParam(':idFormation', $idFormation, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * @param $idSession
 * @param $date_session
 * @param $heure_debut
 * @param $heure_fin
 * @param $lieux
 * @param $date_limite
 * @param $nbmax
 * @return void
 * @throws Exception
 */
function modifierSession($idSession, $date_session, $heure_debut, $heure_fin, $lieux, $date_limite, $nbmax, $id_formation)
{
    $connexion = obtenirConnexion();

    try {
        $sql = "UPDATE sessionformations SET date_session = :date_session, heure_debut = :heure_debut, heure_fin = :heure_fin, lieux = :lieux, date_limite = :date_limite, nb_max = :nbmax, id_formation = :id_formation WHERE id_session = :idSession";
        $stmt = $connexion->prepare($sql);
        $stmt->bindParam(':idSession', $idSession, PDO::PARAM_INT);
        $stmt->bindParam(':date_session', $date_session);
        $stmt->bindParam(':heure_debut', $heure_debut);
        $stmt->bindParam(':heure_fin', $heure_fin);
        $stmt->bindParam(':lieux', $lieux);
        $stmt->bindParam(':date_limite', $date_limite);
        $stmt->bindParam(':nbmax', $nbmax);
        $stmt->bindParam(':id_formation', $id_formation);
        $stmt->execute();
    } catch (PDOException $e) {
        throw new Exception("Erreur PDO lors de la modification de la session : " . $e->getMessage());
    }
}

/**
 * @param $idFormation
 * @param $libelle
 * @param $cout
 * @param $contenu
 * @param $nb_place
 * @param $id_domaine
 * @return void
 * @throws Exception
 */
function modifierFormation($idFormation, $libelle, $cout, $contenu, $nb_place, $id_domaine)
{
    $connexion = obtenirConnexion();

    try {
        $sql = "UPDATE formations SET libelle_formation = :libelle, cout = :cout, contenu = :contenu, nb_place = :nb_place, id_domaine = :id_domaine WHERE id_formation = :idFormation";
        $stmt = $connexion->prepare($sql);
        $stmt->bindParam(':idFormation', $idFormation, PDO::PARAM_INT);
        $stmt->bindParam(':libelle', $libelle);
        $stmt->bindParam(':cout', $cout);
        $stmt->bindParam(':contenu', $contenu);
        $stmt->bindParam(':nb_place', $nb_place);
        $stmt->bindParam(':id_domaine', $id_domaine);
        $stmt->execute();
    } catch (PDOException $e) {
        throw new Exception("Erreur PDO lors de la modification de la formation : " . $e->getMessage());
    }
}

/**
 * @param $idformation
 * @return false|mixed
 */
function getFormationDetails($idformation)
{
    $connexion = obtenirConnexion();

    try {
        $query = "SELECT * FROM formations WHERE id_formation = ?";
        $stmt = $connexion->prepare($query);
        $stmt->execute([$idformation]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Loguer l'erreur dans un fichier de logs par exemple
        error_log("Erreur PDO lors de la récupération des détails de la formation : " . $e->getMessage());
        return false;
    }

}

/**
 * @param $nom
 * @param $prenom
 * @return void
 */
function createIntervenant($nom, $prenom)
{
    $connexion = obtenirConnexion();
    $sql = "INSERT INTO intervenants (nom, prenom) VALUES ('$nom', '$prenom')";

    try {
        // Exécute la requête SQL
        $connexion->exec($sql);

        // Récupère l'ID de l'intervenant nouvellement créé
        $idIntervenant = $connexion->lastInsertId();

        // Retourne l'ID de l'intervenant
        return $idIntervenant;
    } catch (PDOException $e) {

        echo "Error: " . $e->getMessage();
        return null;
    } finally {
        $connexion = null;
    }
}


/**
 * @param $id_intervenant
 * @param $id_session
 * @return void
 */
function intervient($id_intervenant, $id_session)
{
    $connexion = obtenirConnexion();
    $sql = "INSERT INTO intervient (id_intervenant, id_session) VALUES ('$id_intervenant', '$id_session')";

    try {
        $connexion->exec($sql);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    } finally {
        $connexion = null;
    }
}

/**
 * @param $idSession
 * @return array|false
 * @throws Exception
 */
function getIntervenantsSession($idSession)
{
    $connexion = obtenirConnexion();

    try {
        // Requête pour récupérer les intervenants d'une session
        $sql = "SELECT intervenants.id_intervenant, intervenants.nom, intervenants.prenom
                FROM intervenants
                INNER JOIN intervient ON intervenants.id_intervenant = intervient.id_intervenant
                WHERE intervient.id_session = :idSession";

        $stmt = $connexion->prepare($sql);
        $stmt->bindParam(':idSession', $idSession, PDO::PARAM_INT);
        $stmt->execute();

        // Récupérer les résultats de la requête
        $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $resultats;
    } catch (PDOException $e) {
        throw new Exception("Erreur PDO lors de la récupération des intervenants de la session : " . $e->getMessage());
    }
}


//creation_user("Ferrer", "Gabriel", "gabfer258@gmail.com", "Azerty31", "B", "Venez comme vous-etes");
//creation_user("Doumbia", "Bamody", "d.bamody28@gmail.com", "Azerty31", "A", "Club de ping pong");

//creation_formation("Formation D\'escrime", "Cours d\'escrime en groupe de 10", 30, "10 cours sur 5 semaines", "Apprendre les base de l\'escrime", 15, 2);
//creation_formation("Formation Powerpoint", "Decouverte du logiciel", 30, "1 cours ", "Apprendre powerpoint", 10, 1);