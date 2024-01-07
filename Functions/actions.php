<?php
include_once("functions.php"); // Assurez-vous d'inclure votre fichier de connexion

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["action"])) {
        switch ($_POST["action"]) {
            case "creerFormation":
                // Récupérez les données du formulaire
                $libelle = $_POST["libelle"];
                $cout = $_POST["cout"];
                $contenu = $_POST["contenu"];
                $nb_place = $_POST["nb_place"];
                $id_domaine = $_POST["id_domaine"];

                // Appelez la fonction pour créer une formation
                try {
                    creation_formation($libelle, $cout, $contenu, $nb_place, $id_domaine);
                    echo json_encode("formationOk", 'Formation crée avec succès');
                } catch (Exception $e) {
                    echo json_encode("formationKo", 'Erreur lors de la création de la formation');
                }
                break;

            case "creerSession":
                // Récupérez les données du formulaire
                $date_session = $_POST["date_session"];
                $heure_debut = $_POST["heure_debut"];
                $heure_fin = $_POST["heure_fin"];
                $lieux = $_POST["lieux"];
                $date_limite = $_POST["date_limite"];
                $id_formation = $_POST["id_formation"];
                $nbmax = $_POST["nbmax"];
                // Appelez la fonction pour créer une session
                try {
                    creerNouvelleSession($date_session, $heure_debut, $heure_fin, $lieux, $date_limite, $id_formation, $nbmax);
                    echo json_encode("sessionOk", 'Session crée avec succès');
                } catch (Exception $e) {
                    echo json_encode("sessionKo", 'Erreur lors de la création de la session');
                }
                break;
            // Ajoutez d'autres cas pour les actions de modification et de suppression si nécessaire
            default:
                // Gestion d'une action inconnue
                echo "Action inconnue";
                break;
        }
    }
}
?>
