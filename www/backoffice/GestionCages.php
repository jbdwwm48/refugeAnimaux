<?php
session_start();
require '../auth/initDb.php';

$utilisateur_connecte = isset($_SESSION['id_personnel']);

$requete_cages = $pdo->query("SELECT c.id_cage, c.numero, COUNT(a.id_animal) AS occupation FROM cage c LEFT JOIN animal a ON c.id_cage = a.id_cage GROUP BY c.id_cage");
$cages = $requete_cages->fetchAll(PDO::FETCH_ASSOC);

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
    <title>Backoffice Cages</title>

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
            background-color: rgb(175, 150, 182);
            color: white;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini">
    <!-- Navbar -->
    <?php
    include('../nav.php')
    ?>
    <div class="wrapper">
        <!-- Sidebar -->
        <?php include('./sidebar.php') ?>
        <div class="container mt-2">
            <h1 class="mb-4 text-center">Backoffice - Gestion des Animaux</h1>
            <h2 class="my-4 text-center">Occupation des Cages</h2>
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-striped text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>Numéro</th>
                            <th>Occupation</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cages as $cage): ?>
                            <tr>
                                <td><?= $cage['numero'] ?></td>
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: <?= ($cage['occupation'] > 0) ? '100%' : '0%' ?>;" aria-valuenow="<?= $cage['occupation'] ?>" aria-valuemin="0" aria-valuemax="1">
                                            <?= $cage['occupation'] > 0 ? 'Occupée' : 'Libre' ?>
                                        </div>
                                    </div>
                                </td>
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