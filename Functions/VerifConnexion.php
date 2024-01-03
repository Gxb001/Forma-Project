<?php
include("Bd_connect.php");
include("functions.php");
$email = $_POST['email'];
$password = $_POST['mdp'];

$sql = "SELECT prenom,mdp, email, statut, id_utilisateur FROM utilisateurs WHERE email = '$email'";
$result = $connexion->query($sql);
$ligne = $result->fetch();
if ($ligne) {
    $motPasseBdd = $ligne['mdp'];
    if (!password_verify($password, $motPasseBdd)) {
        $data = "activate_logger";
        $url = "../connexion.html.php?data=" . urlencode($data);
        header("Location: " . $url); // Redirection vers la page cible
    } else {
        session_start();
        $_SESSION['user'] = "authentified";
        $_SESSION['id'] = $ligne['id_utilisateur'];
        $_SESSION['role'] = $ligne['statut'];
        $_SESSION['prenom'] = $ligne['prenom'];
        header("Location: ../accueil.html.php");
        exit;
    }
} else {
    $data = "activate_logger";
    $url = "../connexion.html.php?data=" . urlencode($data);
    header("Location: " . $url);
}
$result->closeCursor();
$connexion = null;
?>