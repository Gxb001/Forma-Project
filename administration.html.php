<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CROSL Formations - Administration</title>
    <link rel="stylesheet" href="Styles/style_administration.css">
</head>

<body>
<?php
// Inclure les fichiers d'en-tête et de barre de navigation
include 'includes/header.html.php';
include 'includes/navbar.html.php';


$mysqli = new mysqli("localhost", "root", "", "forma");

if ($mysqli->connect_error) {
    die("La connexion à la base de données a échoué : " . $mysqli->connect_error);
}

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $idFormation = $_POST["formation"];
    $dateDebut = $_POST["date_debut"];
    $lieu = $_POST["lieu"];
    $intervenant = $_POST["intervenant"];

    // Préparer la requête d'insertion
    $query = "INSERT INTO sessionsformation (ID_Formation, DateDebut, Lieu, Intervenant) VALUES (?, ?, ?, ?)";
    $stmt = $mysqli->prepare($query);

    // Exécution de la requête avec les valeurs liées
    $stmt->bind_param("ssss", $idFormation, $dateDebut, $lieu, $intervenant);
    $stmt->execute();

    // Fermer la connexion et la requête
    $stmt->close();
}
?>

<section>
    <h2>Gestion des demandes de formation</h2>
    <h2>Créez une formation</h2>
</section>

<form action="" method="post" class="creeformation">
    <!-- Formulaire de création de formation -->
    <label for="titre">Titre :</label>
    <input type="text" name="titre" required><br><br>

    <label for="description">Description :</label>
    <textarea name="description" rows="2" required></textarea><br><br>

    <label for="domaine">Domaine :</label>
    <input type="text" name="domaine" required><br><br>

    <label for="cout">Coût :</label>
    <input type="number" name="cout" required><br><br>

    <label for="places_disponibles">Places Disponibles :</label>
    <input type="number" name="places_disponibles" required><br><br>

    <button type="submit">Créez</button>
</form>

<form action="" method="post" class="creesession">
    <!-- Formulaire de création de session -->
    <label for="formation">Sélectionner la Formation :</label>
    <select name="formation" required>
        <?php
        // Récupérer les formations depuis la base de données
        $query = "SELECT ID_Formation, Titre FROM formations";
        $result = $mysqli->query($query);

        // Afficher les options de la liste déroulante
        while ($row = $result->fetch_assoc()) {
            echo "<option value=\"{$row['ID_Formation']}\">{$row['Titre']}</option>";
        }

        // Fermer la connexion à la base de données
        $mysqli->close();
        ?>
    </select>

    <label for="date_debut">Date de Début :</label>
    <input type="date" name="date_debut" required><br><br>

    <label for="lieu">Lieu :</label>
    <input type="text" name="lieu" required><br><br>

    <label for="intervenant">Intervenant :</label>
    <input type="text" name="intervenant" required><br><br>

    <button type="submit">Créer Session</button>
</form>

<?php
// Inclure le fichier de pied de page
include 'includes/footer.html';
?>
</body>

</html>