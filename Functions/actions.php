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
                $nbmax = $_POST["nbmax"];
                if ($date_session == "" || $heure_debut == "" || $heure_fin == "" || $lieux == "" || $date_limite == "" || $id_formation == "" || $nbmax == "") {
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
                    creerNouvelleSession($date_session, $heure_debut, $heure_fin, $lieux, $date_limite, $id_formation, $nbmax);
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

            default:
                //Gestion d'une action inconnue
                echo "Action inconnue";
                break;
        }
    }
}
?>
