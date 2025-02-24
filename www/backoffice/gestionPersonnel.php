<?php
session_start();
require '../auth/initDb.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur'])) {
    header('Location: ../index.php');
    exit;
}

// Gestion de la recherche
$search = isset($_GET['search']) ? $_GET['search'] : '';
$requete_personnels = $pdo->prepare("
    SELECT id_personnel, nom, prenom, poste, login 
    FROM personnel 
    WHERE nom LIKE ? OR prenom LIKE ? OR poste LIKE ? OR login LIKE ?
");
$requete_personnels->execute(["%$search%", "%$search%", "%$search%", "%$search%"]);
$personnels = $requete_personnels->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les animaux pour chaque employé avec leur espèce
$animaux_par_personnel = [];
foreach ($personnels as $personnel) {
    $id_personnel = $personnel['id_personnel'];
    $requete_animaux = $pdo->prepare("
        SELECT a.nom, a.genre, a.numero, a.pays, a.date_naissance, a.date_arrivee, a.date_deces, a.historique, a.image, e.nom as espece 
        FROM animal a 
        INNER JOIN s_occuper s ON a.id_animal = s.id_animal 
        INNER JOIN animal_espece ae ON a.id_animal = ae.id_animal 
        INNER JOIN espece e ON ae.id_espece = e.id_espece 
        WHERE s.id_personnel = ?
    ");
    $requete_animaux->execute([$id_personnel]);
    $animaux_par_personnel[$id_personnel] = $requete_animaux->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion du Personnel</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

    <!-- Styles personnalisés -->
    <style>
        .table-responsive {
            max-width: 100%;
            margin: auto;
        }

        .table-sm td,
        .table-sm th {
            padding: 0.5rem;
            font-size: 0.9rem;
        }

        .navbar-custom {
            background-color: rgb(72, 149, 182);
            color: white;
        }

        .animal-details {
            background-color: #f8f9fa;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
        }

        .animal-details table {
            margin-bottom: 0;
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
                    <h1 class="text-center my-4">Gestion du Personnel</h1>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <!-- Tableau -->
                    <div class="row justify-content-center">
                        <div class="col-md-10">
                            <div class="card shadow">
                                <div class="card-header bg-primary text-white">
                                    <h2 class="card-title ">Liste des Employés</h2>
                                    <!-- Ajouter un formulaire de recherche avant le tableau -->
                                    <div class="container-fluid mb-1">
                                        <div class="d-flex justify-content-end">
                                            <form method="GET" class="row g-3">
                                                <div class="col-auto">
                                                    <input type="text" name="search" class="form-control" placeholder="Rechercher..." value="<?= htmlspecialchars($search) ?>">
                                                </div>
                                                <div class="col-auto">
                                                    <button type="submit" class="btn btn-primary">Rechercher</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered table-striped">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>Nom</th>
                                                    <th>Prénom</th>
                                                    <th>Poste</th>
                                                    <th>Login</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($personnels as $personnel) : ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($personnel['nom']) ?></td>
                                                        <td><?= htmlspecialchars($personnel['prenom']) ?></td>
                                                        <td><?= htmlspecialchars($personnel['poste']) ?></td>
                                                        <td><?= htmlspecialchars($personnel['login']) ?></td>
                                                        <td>
                                                            <button class="btn btn-sm btn-info" type="button" data-bs-toggle="collapse" data-bs-target="#animaux-<?= $personnel['id_personnel'] ?>" aria-expanded="false" aria-controls="animaux-<?= $personnel['id_personnel'] ?>">
                                                                Voir les animaux
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    <!-- Section des détails des animaux -->
                                                    <tr class="collapse" id="animaux-<?= $personnel['id_personnel'] ?>">
                                                        <td colspan="5">
                                                            <div class="animal-details">
                                                                <?php if (isset($animaux_par_personnel[$personnel['id_personnel']]) && count($animaux_par_personnel[$personnel['id_personnel']]) > 0) : ?>
                                                                    <table class="table table-sm table-bordered">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Nom</th>
                                                                                <th>Genre</th>
                                                                                <th>Numéro</th>
                                                                                <th>Pays</th>
                                                                                <th>Date de naissance</th>
                                                                                <th>Date d'arrivée</th>
                                                                                <th>Espèce</th>
                                                                                <th>Historique</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <?php foreach ($animaux_par_personnel[$personnel['id_personnel']] as $animal) : ?>
                                                                                <tr>
                                                                                    <td><?= htmlspecialchars($animal['nom']) ?></td>
                                                                                    <td><?= htmlspecialchars($animal['genre']) ?></td>
                                                                                    <td><?= htmlspecialchars($animal['numero']) ?></td>
                                                                                    <td><?= htmlspecialchars($animal['pays']) ?></td>
                                                                                    <td><?= htmlspecialchars($animal['date_naissance']) ?></td>
                                                                                    <td><?= htmlspecialchars($animal['date_arrivee']) ?></td>
                                                                                    <td><?= htmlspecialchars($animal['espece']) ?></td>
                                                                                    <td><?= htmlspecialchars($animal['historique']) ?></td>
                                                                                </tr>
                                                                            <?php endforeach; ?>
                                                                        </tbody>
                                                                    </table>
                                                                <?php else : ?>
                                                                    <p class="text-muted">Pas d'animaux gérés par cet employé.</p>
                                                                <?php endif; ?>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- Bootstrap JS (minimum requis pour le collapse) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>