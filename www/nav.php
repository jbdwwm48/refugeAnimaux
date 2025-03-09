<?php
// Pas besoin de redémarrer la session ici, elle est déjà démarrée dans checkSession.php
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navigation</title>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/bootstrap-icons.css" rel="stylesheet">
    <style>
        .navbar-custom {
            background-color: #58d9ce;
            color: white;
        }

        .navbar-custom .nav-link {
            position: relative;
            display: inline-block;
        }

        .navbar-custom .nav-link .icon-wrapper {
            display: inline-flex;
            align-items: center;
        }

        .navbar-custom .default-icon {
            display: inline;
        }

        .navbar-custom .hover-icon {
            display: none;
        }

        .navbar-custom .nav-link:hover .default-icon {
            display: none;
        }

        .navbar-custom .nav-link:hover .hover-icon {
            display: inline;
        }

        .navbar-custom .nav-link .text {
            font-weight: normal;
            transition: font-weight 0.2s ease;
        }

        .navbar-custom .nav-link:hover .text {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-custom px-4">
        <div class="container-fluid">
            <a href="/index.php"> <img src="/assets/img/logoRefuge.png" height="70" alt="logo du refuge" loading="lazy" /> </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-between px-4" id="navbarNav">
                <div>
                    <ul class="navbar-nav gap-4">
                        <li class="nav-item">
                            <a class="nav-link text-white fs-5 d-flex align-items-center gap-2" href="/index.php">
                                <span class="icon-wrapper">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-house-heart default-icon" viewBox="0 0 16 16">
                                        <path d="M8 6.982C9.664 5.309 13.825 8.236 8 12 2.175 8.236 6.336 5.31 8 6.982" />
                                        <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L2 8.207V13.5A1.5 1.5 0 0 0 3.5 15h9a1.5 1.5 0 0 0 1.5-1.5V8.207l.646.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293zM13 7.207V13.5a.5.5 0 01-.5.5h-9a.5.5 0 0 1-.5-.5V7.207l5-5z" />
                                    </svg>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-house-heart-fill hover-icon" viewBox="0 0 16 16">
                                        <path d="M7.293 1.5a1 1 0 0 1 1.414 0L11 3.793V2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v3.293l2.354 2.353a.5.5 0 0 1-.708.708L8 2.207 1.354 8.854a.5.5 0 1 1-.708-.708z" />
                                        <path d="m14 9.293-6-6-6 6V13.5A1.5 1.5 0 0 0 3.5 15h9a1.5 1.5 0 0 0 1.5-1.5V9.293Zm-6 3.646C10.301 10.59 13.958 11.38 14 13.093c0-1.333-2.149-2.94-5.285-2.94-3.297 0-5.57 1.696-5.704 2.92.033-1.65 3.573-2.46 5.989-.134Z" />
                                    </svg>
                                </span>
                                <span class="text">Accueil</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div>
                    <?php require __DIR__ . '/auth/formulaire.php'; ?>
                </div>
            </div>
        </div>
    </nav>
</body>

</html>