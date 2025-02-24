<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Session expirée</title>
    <script>
        // Afficher une alerte lorsque la page est chargée
        window.onload = function() {
            alert("Votre session a expiré pour cause d'inactivité.");
            window.location.href = "login.php"; // Rediriger vers la page de connexion
        };
    </script>
</head>

<body>
    <noscript>
        <p>Votre session a expiré pour cause d'inactivité. Veuillez <a href="login.php">vous reconnecter</a>.</p>
    </noscript>
</body>

</html>