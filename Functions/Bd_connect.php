<?php
$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'forma';

try {
    $connexion = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    //$connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo 'Connexion réussie';
} catch (PDOException $e) {
    echo 'Erreur de connexion : ' . $e->getMessage();
    die();
}
//Affichage de la connexion
//echo $connexion->getAttribute(PDO::ATTR_CONNECTION_STATUS);
?>