<?php
include_once("functions.php"); //Assurez-vous d'inclure votre fichier de connexion

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["action"])) {
        switch ($_POST["action"]) {
            case "creerFormation":
                //données du formulaire
                $libelle = $_POST["libelle"];
                $cout = $_POST["cout"];
                $contenu = $_POST["contenu"];
                $nb_place = $_POST["nb_place"];
                $id_domaine = $_POST["id_domaine"];
                if ($libelle == "" || $cout == "" || $contenu == "" || $nb_place == "" || $id_domaine == "") {
                    echo json_encode("Champs", 'Veuillez remplir tous les champs');
                    return;
                }
                if ($cout <= 0) {
                    echo json_encode("Cout", 'Le cout doit être supérieur à 0');
                    return;
                }
                if ($nb_place <= 0) {
                    echo json_encode("Place", 'Le nombre de place doit être supérieur à 0');
                    return;
                }

                try {
                    creation_formation($libelle, $cout, $contenu, $nb_place, $id_domaine);
                    echo json_encode("valide", 'Formation crée avec succès');
                } catch (Exception $e) {
                    echo json_encode("err", 'Erreur lors de la création de la formation');
                }
                break;

            case "creerSession":
                //données du formulaire
                $date_session = $_POST["date_session"];
                $heure_debut = $_POST["heure_debut"];
                $heure_fin = $_POST["heure_fin"];
                $lieux = $_POST["lieux"];
                $date_limite = $_POST["date_limite"];
                $id_formation = $_POST["id_formation"];
                $intervenant = $_POST["inter"];
                $parts = explode(";", $intervenant);
                $nom_inter = $parts[0];
                $prenom_inter = $parts[1];
                $nbmax = $_POST["nbmax"];
                if ($date_session == "" || $heure_debut == "" || $heure_fin == "" || $lieux == "" || $date_limite == "" || $id_formation == "" || $nbmax == "" || $intervenant == "" || $nom_inter == "" || $prenom_inter == "") {
                    if ($id_formation && $id_formation == "N/A") {
                        echo json_encode("Formation", "Veuillez choisir une formation");
                        return;
                    }
                    echo json_encode("Champs", 'Veuillez remplir tous les champs');
                    return;
                }
                if ($heure_debut >= $heure_fin) {
                    echo json_encode("Heure", 'L\'heure de début doit être inférieur à l\'heure de fin');
                    return;
                }
                if ($date_session < $date_limite) {
                    echo json_encode("Date", 'La date limite d\'inscription doit être inférieur à la date de session');
                    return;
                }
                if ($nbmax <= 0) {
                    echo json_encode("NbMax", 'Le nombre de place doit être supérieur à 0');
                    return;
                }
                try {
                    $id_inter = createIntervenant($nom_inter, $prenom_inter);
                    $id_se = creerNouvelleSession($date_session, $heure_debut, $heure_fin, $lieux, $date_limite, $id_formation, $nbmax);
                    intervient($id_inter, $id_se);
                    echo json_encode("valide", 'Session crée avec succès');
                } catch (Exception $e) {
                    echo json_encode("err", 'Erreur lors de la création de la session');
                }
                break;
            case "deleteSession":
                $idSession = $_POST["id_session"];
                if ($idSession == "") {
                    echo json_encode("Champs", "Veuillez spécifier l'ID de la session à supprimer");
                    return;
                } elseif ($idSession == "N/A") {
                    echo json_encode("Champs", "Veuillez choisir une session");
                    return;
                }
                try {
                    supprimerSession($idSession);
                    echo json_encode("valide", 'Session supprimée avec succès');
                } catch (Exception $e) {
                    echo json_encode("err", 'Erreur lors de la suppression de la session');
                }
                break;
            case "deleteFormation":
                $idFormation = $_POST["id_formation"];
                if ($idFormation == "") {
                    echo json_encode("id", "Veuillez spécifier l'ID de la formation à supprimer");
                    return;
                }
                try {
                    supprimerFormationEtSessions($idFormation);
                    echo json_encode("valide", 'Formation et sessions supprimées avec succès');
                } catch (Exception $e) {
                    echo json_encode("err", 'Erreur lors de la suppression de la formation et de ses sessions');
                }
                break;
            case "updateFormation":
                $idFormation = $_POST["id_formation"];
                $libelle = $_POST["libelle"];
                $cout = $_POST["cout"];
                $contenu = $_POST["contenu"];
                $nb_place = $_POST["nb_place"];
                $id_domaine = $_POST["id_domaine"];


                $formation = getFormationDetails($idFormation);
                $participants = $formation["nb_participants"];

                if ($idFormation == "") {
                    echo json_encode(array('error' => "Veuillez spécifier l'ID de la formation à modifier"));
                    return;
                } else if ($libelle == "" || $cout == "" || $contenu == "" || $nb_place == "" || $id_domaine == "") {
                    echo json_encode(array('error' => 'Veuillez remplir tous les champs'));
                    return;
                } else if ($cout <= 0) {
                    echo json_encode(array('error' => 'Le cout doit être supérieur à 0'));
                    return;
                } else if ($nb_place <= 0) {
                    echo json_encode(array('error' => 'Le nombre de place doit être supérieur à 0'));
                    return;
                } else if ($nb_place < $participants) {
                    echo json_encode(array('error' => 'Le nombre de place doit être supérieur ou égal au nombre de place par défaut'));
                    return;
                } else {
                    try {
                        modifierFormation($idFormation, $libelle, $cout, $contenu, $nb_place, $id_domaine);
                        echo json_encode(array('valide' => 'Formation modifiee avec succes'));
                    } catch (Exception $e) {
                        echo json_encode(array('error' => 'Erreur lors de la modification de la formation'));
                    }
                    break;
                }

            case "updateSession":
                $idSession = $_POST["id_session"];
                $date_session = $_POST["date_sessione"];
                $heure_debut = $_POST["heure_debute"];
                $heure_fin = $_POST["heure_fine"];
                $lieux = $_POST["lieuxe"];
                $date_limite = $_POST["date_limitee"];
                $id_formation = $_POST["id_formatione"];
                $nbmax = $_POST["nbmaxe"];
                $intervenant = $_POST["intere"];
                $parts = explode(";", $intervenant);
                $nom_inter = $parts[0];
                $prenom_inter = $parts[1];

                if ($idSession == "") {
                    echo json_encode(array("error" => "Veuillez spécifier l'ID de la session à modifier"));
                    return;
                }
                $session = getSessionDetails($idSession);
                $participants = $session["nb_participant"];
                if ($date_session == "" || $heure_debut == "" || $heure_fin == "" || $lieux == "" || $date_limite == "" || $id_formation == "" || $nbmax == "" || $intervenant == "" || $nom_inter == "" || $prenom_inter == "") {
                    if ($id_formation && $id_formation == "N/A") {
                        echo json_encode(array("error" => "Veuillez choisir une formation"));
                        return;
                    }
                    echo json_encode(array("error" => 'Veuillez remplir tous les champs'));
                    return;
                }
                if ($heure_debut >= $heure_fin) {
                    echo json_encode(array("error" => 'L\'heure de début doit être inférieur à l\'heure de fin'));
                    return;
                }
                if ($date_session < $date_limite) {
                    echo json_encode(array("error" => 'La date limite d\'inscription doit être inférieur à la date de session'));
                    return;
                }
                if ($nbmax <= 0) {
                    echo json_encode(array("error" => 'Le nombre de place doit être supérieur à 0'));
                    return;
                }
                if ($nbmax < $participants) {
                    echo json_encode(array("error" => 'Le nombre de place doit être supérieur ou égal au nombre de place par défaut'));
                    return;
                }
                try {
                    modifierSession($idSession, $date_session, $heure_debut, $heure_fin, $lieux, $date_limite, $nbmax, $id_formation);
                    echo json_encode(array("valide" => 'Session modifiée avec succès'));
                } catch (Exception $e) {
                    echo json_encode(array("error" => 'Erreur lors de la modification de la session'));
                }
                break;

            default:
                //Gestion d'une action inconnue
                echo "Action inconnue";
                break;
        }
    }
} else {
    echo "Méthode non autorisée";

}
?>
