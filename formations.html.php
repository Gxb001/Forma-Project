<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Styles/style_administration.css">
    <title>Page d'Administration</title>
</head>
<body>
<?php
include 'includes/header.html.php';
include 'includes/navbar.html.php';
?>
<h1>Page d'Administration</h1>

<!-- Formulaire pour créer une nouvelle formation -->
<h2>Créer une nouvelle formation</h2>
<form action="Functions/process.php" method="post">
    <label for="titre">Titre :</label>
    <input type="text" name="titre" required><br>

    <label for="description">Description :</label>
    <textarea name="description" required></textarea><br>

    <label for="domaine">Domaine :</label>
    <input type="text" name="domaine" required><br>

    <label for="cout">Coût :</label>
    <input type="text" name="cout" required><br>

    <label for="places_disponibles">Places disponibles :</label>
    <input type="text" name="places_disponibles" required><br>

    <input type="submit" name="creer_formation" value="Créer Formation">
</form>

<h2>Modifier ou Supprimer une Formation</h2>
<!-- Formulaire pour rechercher et sélectionner une formation à modifier ou supprimer -->
<form action="Functions/admin.php" method="post">
    <label for="formation_id">Sélectionnez une formation :</label>
    <select name="formation_id" required>
        <?php
        // Récupération des formations depuis la base de données
        $resultat = $connexion->query("SELECT ID_Formation, Titre FROM Formations");

        // Affichage des options dans le menu déroulant
        while ($row = $resultat->fetch(PDO::FETCH_ASSOC)) {
            echo '<option value="' . $row['ID_Formation'] . '">' . $row['Titre'] . '</option>';
        }
        ?>
    </select>
    <input type="submit" name="select_formation" value="Sélectionner">
</form>

<?php
// Traitement de la sélection d'une formation pour modification ou suppression
if (isset($_POST['select_formation'])) {
    $selected_formation_id = $_POST['formation_id'];

    // Récupération des détails de la formation sélectionnée
    $requete_formation = $connexion->prepare("SELECT * FROM Formations WHERE ID_Formation = ?");
    $requete_formation->execute([$selected_formation_id]);
    $formation = $requete_formation->fetch(PDO::FETCH_ASSOC);

    // Affichage du formulaire de modification
    echo '<h2>Modifier la Formation</h2>';
    echo '<form action="admin.php" method="post">';
    echo '<input type="hidden" name="formation_id" value="' . $formation['ID_Formation'] . '">';
    echo '<label for="titre">Titre :</label>';
    echo '<input type="text" name="titre" value="' . $formation['Titre'] . '" required><br>';
    // Ajoutez d'autres champs ici...

    echo '<input type="submit" name="modifier_formation" value="Modifier">';
    echo '<input type="submit" name="supprimer_formation" value="Supprimer" onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer cette formation ?\');">';
    echo '</form>';
}
?>

</body>
</html>