<?php

include("Bd_connect.php");
/**
 * @param $nom
 * @param $prenom
 * @param $email
 * @param $mdp
 * @param $status
 * @param $association
 * @return void
 */
function creation_user($nom, $prenom, $email, $mdp, $status, $association)
{
    include("Bd_connect.php");
    $mdp = password_hash($mdp, PASSWORD_DEFAULT);
    $sql = "INSERT INTO utilisateurs (nom, prenom, email, mdp, statut, association) VALUES ('$nom', '$prenom', '$email', '$mdp', '$status', '$association')";
    $result = $connexion->query($sql);
    $result->closeCursor();
    $connexion = null;
}

/**
 * @param $nom
 * @param $description
 * @param $date_debut
 * @param $date_fin
 * @param $nb_participants
 * @param $nb_participants_max
 * @param $association
 * @return void
 */
function creation_formation($nom, $description, $date_debut, $date_fin, $nb_participants, $nb_participants_max, $association)
{
    include("Bd_connect.php");
    $sql = "INSERT INTO formations (nom, description, date_debut, date_fin, nb_participants, nb_participants_max, association) VALUES ('$nom', '$description', '$date_debut', '$date_fin', '$nb_participants', '$nb_participants_max', '$association')";
    $result = $connexion->query($sql);
    $result->closeCursor();
    $connexion = null;
}

/**
 * @return mixed
 */
function getFormations()
{
    global $connexion;
    $sql = "SELECT * FROM formations";
    $result = $connexion->query($sql);
    $result->closeCursor();
    $connexion = null;
    return $result;

}
//creation_user("Ferrer", "Gabriel", "gabfer258@gmail.com", "Azerty31", "B", "Venez comme vous-etes");
//creation_user("Doumbia", "Bamody", "d.bamody28@gmail.com", "Azerty31", "A", "Club de ping pong");