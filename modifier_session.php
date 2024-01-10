<?php

include 'includes/header.html.php';
include 'includes/navbar.html.php';

include 'Functions/Bd_connect.php';


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_modification_session"])) {
    $sessionId = $_POST["session_id"];

   
    $dateSession = $_POST["date_session"];
    $heureDebut = $_POST["heure_debut"];
    $heureFin = $_POST["heure_fin"];
    $lieux = $_POST["lieux"];
    $nbParticipant = $_POST["nb_participant"];
    $dateLimite = $_POST["date_limite"];
    $idFormation = $_POST["id_formation"];
    $nbMax = $_POST["nb_max"];

    // Préparer et exécuter la requête de mise à jour de la session
    $queryUpdateSession = "UPDATE sessionformations SET date_session=?, heure_debut=?, heure_fin=?, lieux=?, nb_participant=?, date_limite=?, id_formation=?, nb_max=? WHERE id_session=?";
    $stmtUpdateSession = $mysqli->prepare($queryUpdateSession);
    $stmtUpdateSession->bind_param("ssssisiii", $dateSession, $heureDebut, $heureFin, $lieux, $nbParticipant, $dateLimite, $idFormation, $nbMax, $sessionId);
    $stmtUpdateSession->execute();
    $stmtUpdateSession->close();
}

// Récupérer toutes les sessions depuis la base de données
$querySessions = "SELECT * FROM sessionformations";
$resultSessions = $mysqli->query($querySessions);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Sessions - CROSL Formations</title>
    <link rel="stylesheet" href="../Styles/style_administration.css">
</head>

<body>
    <section>
        <h2>Liste des Sessions</h2>
    </section>

    <table>
        <tr>
            <th>ID Session</th>
            <th>Date de session</th>
            <th>Heure de début</th>
            <th>Heure de fin</th>
            <th>Lieux</th>
            <th>Nombre de participants</th>
            <th>Date limite</th>
            <th>Formation</th>
            <th>Nombre maximum de participants</th>
            <th>Action</th>
        </tr>
        <?php
        while ($rowSession = $resultSessions->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$rowSession['id_session']}</td>";
            echo "<td>{$rowSession['date_session']}</td>";
            echo "<td>{$rowSession['heure_debut']}</td>";
            echo "<td>{$rowSession['heure_fin']}</td>";
            echo "<td>{$rowSession['lieux']}</td>";
            echo "<td>{$rowSession['nb_participant']}</td>";
            echo "<td>{$rowSession['date_limite']}</td>";
            echo "<td>{$rowSession['id_formation']}</td>";
            echo "<td>{$rowSession['nb_max']}</td>";
            echo "<td>
                    <form action=\"\" method=\"post\">
                        <input type=\"hidden\" name=\"session_id\" value=\"{$rowSession['id_session']}\">
                        <button type=\"submit\" name=\"modifier_session\">Modifier</button>
                    </form>
                  </td>";
            echo "</tr>";
        }
        ?>
    </table>

    <?php
    // Afficher le formulaire de modification de session s'il est soumis
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["modifier_session"])) {
        $sessionId = $_POST["session_id"];

        // Récupérer les données de la session à modifier
        $querySelectSession = "SELECT * FROM sessionformations WHERE id_session=?";
        $stmtSelectSession = $mysqli->prepare($querySelectSession);
        $stmtSelectSession->bind_param("i", $sessionId);
        $stmtSelectSession->execute();
        $resultSelectSession = $stmtSelectSession->get_result();
        $sessionToModify = $resultSelectSession->fetch_assoc();
        $stmtSelectSession->close();
    ?>
        <!-- Afficher le formulaire prérempli -->
        <form action="" method="post">
            <input type="hidden" name="session_id" value="<?php echo $sessionToModify['id_session']; ?>">
            <label for="date_session">Date de session:</label>
            <input type="date" name="date_session" value="<?php echo $sessionToModify['date_session']; ?>" required>
            <br>
            <label for="heure_debut">Heure de début:</label>
            <input type="time" name="heure_debut" value="<?php echo $sessionToModify['heure_debut']; ?>" required>
            <br>
            <label for="heure_fin">Heure de fin:</label>
            <input type="time" name="heure_fin" value="<?php echo $sessionToModify['heure_fin']; ?>" required>
            <br>
            <label for="lieux">Lieux:</label>
            <input type="text" name="lieux" value="<?php echo $sessionToModify['lieux']; ?>" required>
            <br>
            <label for="nb_participant">Nombre de participants:</label>
            <input type="number" name="nb_participant" value="<?php echo $sessionToModify['nb_participant']; ?>" required>
            <br>
            <label for="date_limite">Date limite:</label>
            <input type="date" name="date_limite" value="<?php echo $sessionToModify['date_limite']; ?>" required>
            <br>
            <label for="id_formation">Formation:</label>
            <input type="number" name="id_formation" value="<?php echo $sessionToModify['id_formation']; ?>" required>
            <br>
            <label for="nb_max">Nombre maximum de participants:</label>
            <input type="number" name="nb_max" value="<?php echo $sessionToModify['nb_max']; ?>" required>
            <br>
            <button type="submit" name="submit_modification_session">Enregistrer</button>
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
