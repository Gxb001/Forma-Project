<?php
include_once("functions.php");

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET["idFormation"])) {
        $idFormation = $_GET["idFormation"];
        $sessions = getSessionsByFormation($idFormation);
        echo json_encode($sessions);
    }
}
?>
