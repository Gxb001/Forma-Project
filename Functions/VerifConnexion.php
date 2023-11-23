<?php
include("Bd_connect.php");
$email = $_POST['email'];
$password = $_POST['mdp'];

$sql = "SELECT mdp, email, statut, id_utilisateur FROM utilisateurs WHERE email = '$email'";
$result = $connexion->query($sql);
$ligne = $result->fetch();
if ($ligne) {
    $motPasseBdd = $ligne['mdp'];
    if (!CheckPasswordHash($mdp, $motPasseBdd)) {
        //Wrong password message d'erreur
    } else if (CheckPasswordHash($mdp, $motPasseBdd)) {
        session_start();
        $_SESSION['user'] = "authentified";
        $_SESSION['id'] = $ligne['id_utilisateur'];
        $_SESSION['email'] = $ligne['email'];
        $_SESSION['role'] = $ligne['statut'];
        header("Location: accueil.html.php");
        // On quitte le script courant
        exit;
    }
} else {
    //Wrong user message d'erreur
}
$result->closeCursor();
$connexion = null;
?>


