<?php

// Inclure votre fichier de fonctions
include 'functions.php';

// Utilisez votre fonction pour obtenir les formations
$formations = getFormations();

// Convertir les résultats en tableau associatif
$formationsArray = $formations->fetchAll(PDO::FETCH_ASSOC);

// Renvoyer les formations au format JSON
echo json_encode($formationsArray);

?>
