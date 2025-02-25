<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Déconnexion</title>
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.1.0/css/adminlte.min.css">
    <!-- Optional Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .logout-message {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
        }
    </style>
</head>

<body>
    <div class="logout-message">
        <h1 class="text-danger">Vous avez été déconnecté.</h1>
        <p class="text-muted">Redirection vers la page d'accueil...</p>
    </div>
    <script>
        // Redirection après 3 secondes
        setTimeout(function() {
            window.location.href = "../index.php";
        }, 3000);
    </script>
</body>

</html>