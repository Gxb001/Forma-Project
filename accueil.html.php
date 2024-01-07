<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CROSL Formations</title>
    <link rel="stylesheet" href="Styles/style_accueil.css">
    <link rel="stylesheet" href="Styles/Font.css">
    <link rel="stylesheet" href="Styles/style_scrollbar.css">
</head>

<body>
<?php include 'includes/navbar.html.php'; ?>

<main>
    <?php include "includes/carousel.html"; ?>
</main>

<?php include 'includes/footer.html'; ?>
<?php include 'includes/loading.html'; ?>
</body>
<script>
    window.addEventListener('load', function () {
        document.getElementById('loader-container').style.display = "none";
    });
    window.addEventListener('beforeunload', function () {
        document.getElementById('loader-container').style.display = "flex";
    });
</script>

</html>