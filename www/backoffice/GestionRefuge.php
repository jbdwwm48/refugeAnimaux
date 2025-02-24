<?php
session_start();
require '../auth/initDb.php';

$utilisateur_connecte = isset($_SESSION['id_personnel']);

$requete_animaux = $pdo->query("SELECT a.id_animal, a.nom, a.genre, a.numero, c.numero AS cage FROM animal a LEFT JOIN cage c ON a.id_cage = c.id_cage");
$animaux = $requete_animaux->fetchAll(PDO::FETCH_ASSOC);

$personnels = [];
if ($utilisateur_connecte) {
    $requete_personnels = $pdo->query("SELECT id_personnel, nom, prenom, poste FROM personnel");
    $personnels = $requete_personnels->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backoffice Refuge</title>

    <!-- Bootstrap & AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <!-- Styles personnalisés-->
    <style>
        .table-responsive {
            max-width: 800px;
            margin: auto;
        }

        .table-sm td,
        .table-sm th {
            padding: 0.3rem;
            font-size: 0.85rem;
        }

        .progress-bar {
            font-size: 0.75rem;
        }

        .navbar-custom {
            background-color: rgb(72, 149, 182);
            color: white;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini">
    <!-- Navbar -->
    <?php
    include('../nav.php')
    ?>
    <div class="wrapper mt-2">
        <!-- Sidebar -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="#" class="brand-link">
                <span class="brand-text font-weight-light">AdminLTE</span>
            </a>
            <div class="sidebar">
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column">
                        <li class="nav-item">
                            <a href="../backoffice/dashboard.php" class="nav-link active">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Gestion du personnel</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="../backoffice/GestionRefuge.php" class="nav-link active">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Liste des animaux</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="../backoffice/GestionCages.php" class="nav-link active">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Gestion des Cages</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>
        <div class="container">
            <h1 class="mb-4 text-center">Backoffice - Gestion des Animaux</h1>

            <h2 class="my-4 text-center">Liste des Animaux</h2>
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-striped text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Genre</th>
                            <th>Numéro</th>
                            <th>Cage</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($animaux as $animal): ?>
                            <tr>
                                <td><?= $animal['id_animal'] ?></td>
                                <td><?= htmlspecialchars($animal['nom']) ?></td>
                                <td><?= htmlspecialchars($animal['genre']) ?></td>
                                <td><?= htmlspecialchars($animal['numero']) ?></td>
                                <td><?= $animal['cage'] ?? 'Non assignée' ?></td>
                                <td><a href="localhost/backoffice/fiche_animal.php?id=3" class="btn btn-info btn-sm">Voir</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($utilisateur_connecte): ?>
                <h2 class="mt-4 text-center">Liste des Employés</h2>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Poste</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($personnels as $personnel): ?>
                                <tr>
                                    <td><?= $personnel['id_personnel'] ?></td>
                                    <td><?= htmlspecialchars($personnel['nom']) ?></td>
                                    <td><?= htmlspecialchars($personnel['prenom']) ?></td>
                                    <td><?= htmlspecialchars($personnel['poste']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
</body>

</html>