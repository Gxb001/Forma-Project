<?php
session_start();
if (isset($_SESSION['user'])) {
    try {
        session_unset();
        session_destroy();
    } catch (Exception $e) {
        echo $e;
    }

}
header("Location: ../accueil.html.php");
exit();
?>