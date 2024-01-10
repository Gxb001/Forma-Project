<?php
// Inclure les fichiers d'en-tête et de barre de navigation
include 'includes/header.html.php';
include 'includes/navbar.html.php';

// Connectez-vous à la base de données
$mysqli = new mysqli("localhost", "root", "", "forma");

if ($mysqli->connect_error) {
    die("La connexion à la base de données a échoué : " . $mysqli->connect_error);
}

// Vérifier si le formulaire de modification a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_modification"])) {
    $formationId = $_POST["formation_id"];

    // Récupérer les données du formulaire de modification
    $libelleFormation = $_POST["libelle_formation"];
    $cout = $_POST["cout"];
    $contenu = $_POST["contenu"];
    $nbPlace = $_POST["nb_place"];
    $idDomaine = $_POST["id_domaine"];
    $nbParticipants = $_POST["nb_participants"];

    // Préparer et exécuter la requête de mise à jour de la formation
    $queryUpdate = "UPDATE formations SET libelle_formation=?, cout=?, contenu=?, nb_place=?, id_domaine=?, nb_participants=? WHERE ID_Formation=?";
    $stmtUpdate = $mysqli->prepare($queryUpdate);
    $stmtUpdate->bind_param("ssssiii", $libelleFormation, $cout, $contenu, $nbPlace, $idDomaine, $nbParticipants, $formationId);
    $stmtUpdate->execute();
    $stmtUpdate->close();
}

// Récupérer toutes les formations depuis la base de données
$queryFormations = "SELECT * FROM formations";
$resultFormations = $mysqli->query($queryFormations);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Formations - CROSL Formations</title>
    <link rel="stylesheet" href="../Styles/style_administration.css">
</head>

<body>
    <section>
        <h2>Liste des Formations</h2>
    </section>

    <table>
        <tr>
            <th>ID Formation</th>
            <th>Libellé de la Formation</th>
            <th>Coût</th>
            <th>Contenu</th>
            <th>Nombre de Places</th>
            <th>ID Domaine</th>
            <th>Nombre de Participants</th>
            <th>Action</th>
        </tr>
        <?php
        while ($rowFormation = $resultFormations->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$rowFormation['id_formation']}</td>";
            echo "<td>{$rowFormation['libelle_formation']}</td>";
            echo "<td>{$rowFormation['cout']}</td>";
            echo "<td>{$rowFormation['contenu']}</td>";
            echo "<td>{$rowFormation['nb_place']}</td>";
            echo "<td>{$rowFormation['id_domaine']}</td>";
            echo "<td>{$rowFormation['nb_participants']}</td>";
            echo "<td>
                    <form action=\"\" method=\"post\">
                        <input type=\"hidden\" name=\"formation_id\" value=\"{$rowFormation['id_formation']}\">
                        <button type=\"submit\" name=\"modifier_formation\">Modifier</button>
                    </form>
                  </td>";
            echo "</tr>";
        }
        ?>
    </table>

    <?php
    // Afficher le formulaire de modification s'il est soumis
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["modifier_formation"])) {
        $formationId = $_POST["formation_id"];

        // Récupérer les données de la formation à modifier
        $querySelect = "SELECT * FROM formations WHERE ID_Formation=?";
        $stmtSelect = $mysqli->prepare($querySelect);
        $stmtSelect->bind_param("i", $formationId);
        $stmtSelect->execute();
        $resultSelect = $stmtSelect->get_result();
        $formationToModify = $resultSelect->fetch_assoc();
        $stmtSelect->close();
    ?>
        <!-- Afficher le formulaire prérempli -->
        <form action="" method="post">
            <input type="hidden" name="formation_id" value="<?php echo $formationToModify['id_formation']; ?>">
            <label for="libelle_formation">Libellé de la Formation:</label>
            <input type="text" name="libelle_formation" value="<?php echo $formationToModify['libelle_formation']; ?>" required>
            <br>
            <label for="cout">Coût:</label>
            <input type="text" name="cout" value="<?php echo $formationToModify['cout']; ?>" required>
            <br>
            <label for="contenu">Contenu:</label>
            <textarea name="contenu" required><?php echo $formationToModify['contenu']; ?></textarea>
            <br>
            <label for="nb_place">Nombre de Places:</label>
            <input type="number" name="nb_place" value="<?php echo $formationToModify['nb_place']; ?>" required>
            <br>
            <label for="id_domaine">ID Domaine:</label>
            <select name="id_domaine" required>
                <option value="1" <?php echo ($formationToModify['id_domaine'] == 1 ? 'selected' : ''); ?>>Domaine 1</option>
                <option value="2" <?php echo ($formationToModify['id_domaine'] == 2 ? 'selected' : ''); ?>>Domaine 2</option>
                <!-- Ajoutez d'autres options de domaine selon vos besoins -->
            </select>
            <br>
            <label for="nb_participants">Nombre de Participants:</label>
            <input type="number" name="nb_participants" value="<?php echo $formationToModify['nb_participants']; ?>" required>
            <br>
            <button type="submit" name="submit_modification">Enregistrer</button>
        </form>
    <?php
    }

    // Fermer la connexion à la base de données
    $mysqli->close();

    // Inclure le fichier de pied de page
    include 'includes/footer.html';
    ?>
</body>

</html>
