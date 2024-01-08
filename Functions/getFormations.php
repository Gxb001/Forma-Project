<?php

include 'functions.php';

$formations = getFormations();

$formationsArray = $formations->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($formationsArray);

?>
