<?php
session_start();
require '../auth/initDb.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur'])) {
    header('Location: ../index.php');
    exit;
}

// Récupérer les données pour les cartes
$requete_animaux = $pdo->query("SELECT COUNT(*) AS total_animaux FROM animal");
$total_animaux = $requete_animaux->fetch(PDO::FETCH_ASSOC)['total_animaux'];

$requete_cages = $pdo->query("
    SELECT COUNT(*) AS total_cages, SUM(IF(a.id_animal IS NOT NULL, 1, 0)) AS cages_occupees 
    FROM cage c 
    LEFT JOIN animal a ON c.id_cage = a.id_cage
");
$cages_data = $requete_cages->fetch(PDO::FETCH_ASSOC);
$total_cages = $cages_data['total_cages'];
$cages_occupees = $cages_data['cages_occupees'];
$taux_occupation = $total_cages > 0 ? round(($cages_occupees / $total_cages) * 100, 2) : 0;

$requete_employes = $pdo->query("SELECT COUNT(*) AS total_employes FROM personnel");
$total_employes = $requete_employes->fetch(PDO::FETCH_ASSOC)['total_employes'];

$requete_especes = $pdo->query("SELECT COUNT(DISTINCT id_espece) AS total_especes FROM animal_espece");
$total_especes = $requete_especes->fetch(PDO::FETCH_ASSOC)['total_especes'];

// Récupérer les données pour le diagramme camembert
$requete_especes_animaux = $pdo->query("
    SELECT e.nom AS espece, COUNT(a.id_animal) AS nombre_animaux 
    FROM animal a 
    INNER JOIN animal_espece ae ON a.id_animal = ae.id_animal 
    INNER JOIN espece e ON ae.id_espece = e.id_espece 
    GROUP BY e.nom
");
$especes_animaux = $requete_especes_animaux->fetchAll(PDO::FETCH_ASSOC);

// Préparer les données pour Chart.js
$labels = [];
$data = [];
$colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40']; // Couleurs pour le graphique

foreach ($especes_animaux as $espece) {
    $labels[] = $espece['espece'];
    $data[] = $espece['nombre_animaux'];
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

    <!-- Icônes Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Styles personnalisés -->
    <style>
        .card-custom {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .card-custom:hover {
            transform: translateY(-5px);
        }

        .card-custom .bi {
            font-size: 2rem;
            margin-right: 10px;
        }

        .progress {
            height: 10px;
            border-radius: 5px;
        }

        .progress-bar {
            background-color: #28a745;
        }

        .chart-container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
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
                    <h1 class="text-center my-4">Tableau de Bord</h1>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <!-- Cartes d'informations -->
                    <div class="row">
                        <!-- Carte Animaux -->
                        <div class="col-md-3">
                            <div class="card card-custom bg-primary text-white mb-4">
                                <div class="card-body d-flex align-items-center">
                                    <i class="bi bi-heart-fill"></i>
                                    <div>
                                        <h5 class="card-title">Animaux</h5>
                                        <p class="card-text"><?= $total_animaux ?> animaux</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Carte Cages -->
                        <div class="col-md-3">
                            <div class="card card-custom bg-success text-white mb-4">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-house-door-fill"></i>
                                        <div>
                                            <h5 class="card-title">Cages</h5>
                                            <p class="card-text"><?= $taux_occupation ?>% occupées</p>
                                        </div>
                                    </div>
                                    <div class="progress mt-2">
                                        <div class="progress-bar" style="width: <?= $taux_occupation ?>%;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Carte Employés -->
                        <div class="col-md-3">
                            <div class="card card-custom bg-warning text-dark mb-4">
                                <div class="card-body d-flex align-items-center">
                                    <i class="bi bi-people-fill"></i>
                                    <div>
                                        <h5 class="card-title">Employés</h5>
                                        <p class="card-text"><?= $total_employes ?> employés</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Carte Espèces -->
                        <div class="col-md-3">
                            <div class="card card-custom bg-info text-white mb-4">
                                <div class="card-body d-flex align-items-center">
                                    <i class="bi bi-tree-fill"></i>
                                    <div>
                                        <h5 class="card-title">Espèces</h5>
                                        <p class="card-text"><?= $total_especes ?> espèces</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section Diagramme Camembert -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card card-custom">
                                <div class="card-header bg-secondary text-white">
                                    <h5 class="card-title">Répartition des espèces</h5>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="especesChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- Script pour Chart.js -->
    <script>
        const ctx = document.getElementById('especesChart').getContext('2d');
        const especesChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: <?= json_encode($labels) ?>,
                datasets: [{
                    data: <?= json_encode($data) ?>,
                    backgroundColor: <?= json_encode($colors) ?>,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: {
                                size: 14
                            }
                        }
                    }
                }
            }
        });
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>