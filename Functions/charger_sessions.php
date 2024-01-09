<?php
include_once("functions.php");
session_start();
if (isset($_POST['idFormation'])) {
    $idFormation = $_POST['idFormation'];

    $sessions = getSessionsFormation($idFormation);

    echo json_encode($sessions->fetchAll(PDO::FETCH_ASSOC));
} else {
    echo json_encode(['error' => 'ID de formation manquant']);
}
?>
