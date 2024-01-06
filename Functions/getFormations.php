<?php

include 'functions.php';

$formations = getFormations();

//Convertir les résultats en tableau associatif
$formationsArray = $formations->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($formationsArray);

?>
