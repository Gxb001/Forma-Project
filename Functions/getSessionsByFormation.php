<?php
include_once("functions.php"); // Assurez-vous d'inclure votre fichier de connexion

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET["idFormation"])) {
        $idFormation = $_GET["idFormation"];

        try {
            $sessions = getSessionsByFormation($idFormation);
            echo json_encode($sessions);
        } catch (Exception $e) {
            echo json_encode([]);
        }
    }
}
?>
