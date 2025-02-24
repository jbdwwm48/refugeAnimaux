<?php
session_start();
require '../auth/initDb.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur'])) {
    header('Location: ../index.php');
    exit;
}

// Récupérer les cages avec les animaux associés
$requete_cages = $pdo->query("
    SELECT c.id_cage, c.numero, c.allee, c.salle, a.id_animal, a.nom AS nom_animal, e.nom AS espece 
    FROM cage c 
    LEFT JOIN animal a ON c.id_cage = a.id_cage 
    LEFT JOIN animal_espece ae ON a.id_animal = ae.id_animal 
    LEFT JOIN espece e ON ae.id_espece = e.id_espece
");
$cages = $requete_cages->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Cages</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

    <!-- Styles personnalisés -->
    <style>
        .cage-card {
            width: 200px;
            height: 200px;
            margin: 10px;
            padding: 15px;
            border-radius: 10px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s;
        }

        .cage-card:hover {
            transform: scale(1.05);
        }

        .cage-card.occupied {
            background-color: rgb(47, 197, 85);
            /* Vert pour les cages occupées */
        }

        .cage-card.vacant {
            background-color: rgb(25, 102, 184);
            /* Bleu pour les cages libres */
        }

        .cage-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .cage-card h3 {
            margin: 0;
            font-size: 1.5rem;
        }

        .cage-card p {
            margin: 5px 0;
            font-size: 1rem;
        }

        .navbar-custom {
            background-color: rgb(72, 149, 182);
            color: white;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini">
    <!-- Navbar -->
    <?php include('../nav.php'); ?>

    <div class="wrapper">
        <!-- Sidebar -->
        <?php include('./sidebar.php') ?>
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <h1 class="text-center my-4">Gestion des Cages</h1>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <!-- Grille des cages -->
                    <div class="cage-grid">
                        <?php foreach ($cages as $cage): ?>
                            <div class="cage-card <?= $cage['id_animal'] ? 'occupied' : 'vacant' ?>">
                                <h3>Cage <?= htmlspecialchars($cage['numero']) ?></h3>
                                <?php if ($cage['id_animal']): ?>
                                    <p><?= htmlspecialchars($cage['nom_animal']) ?></p>
                                    <p>ID: <?= htmlspecialchars($cage['id_animal']) ?></p>
                                    <p>Espèce: <?= htmlspecialchars($cage['espece']) ?></p>
                                <?php else: ?>
                                    <p>Libre</p>
                                <?php endif; ?>
                                <p>Allée: <?= htmlspecialchars($cage['allee']) ?></p>
                                <p>Salle: <?= htmlspecialchars($cage['salle']) ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- Bootstrap JS (minimum requis pour le collapse) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>