<?php
session_start();
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Déconnexion</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Style cohérent avec les autres pages */
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .navbar-custom {
            background-color: #58d9ce;
            color: white;
        }

        .container-custom {
            min-height: 80vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .loader {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #58d9ce;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .btn-custom {
            background-color: #58d9ce;
            border-color: #58d9ce;
            color: white;
            transition: background-color 0.3s ease;
        }

        .btn-custom:hover {
            background-color: #46b8ac;
            border-color: #46b8ac;
        }

        footer {
            background-color: #58d9ce;
            color: white;
            padding: 20px 0;
            position: relative;
            bottom: 0;
            width: 100%;
            text-align: center;
        }
    </style>
</head>

<body>
    <!-- Inclure la barre de navigation (comme dans vos autres pages) -->
    <?php include('../nav.php'); ?>

    <!-- Contenu principal -->
    <div class="container container-custom">
        <h1 class="mb-4">Vous avez été déconnecté</h1>
        <p class="mb-3">Votre session a expiré ou vous vous êtes déconnecté.</p>
        <p>Vous serez automatiquement redirigé dans <span id="countdown">5</span> secondes...</p>

        <!-- Animation de chargement (loader) -->
        <div class="loader" id="loader"></div>

        <!-- Bouton pour redirection manuelle -->
        <a href="/index.php" class="btn btn-custom mt-3">Retour à l'accueil maintenant</a>
    </div>

    <!-- Inclure le footer (comme dans vos autres pages) -->
    <?php include('../footer.php'); ?>

    <!-- Scripts -->
    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script>
        // Compte à rebours et redirection automatique après 5 secondes
        let countdown = 5;
        const countdownElement = document.getElementById('countdown');
        const loaderElement = document.getElementById('loader');

        const countdownInterval = setInterval(() => {
            countdown--;
            countdownElement.textContent = countdown;

            if (countdown <= 0) {
                clearInterval(countdownInterval);
                loaderElement.style.display = 'none'; // Cacher le loader
                window.location.href = '/index.php'; // Redirection
            }
        }, 1000);
    </script>
</body>

</html>