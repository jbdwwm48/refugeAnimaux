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
            height: 100%;
            /* Uniformiser la hauteur */
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
            background-color: rgba(251, 187, 201, 0.9);
        }

        .chart-container {
            width: 50%;
            /* Réduire la taille de la chart de 50% */
            margin: auto;
            padding: 20px;
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
                    <h1 class="text-center my-4">Tableau de Bord</h1>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <!-- Cartes d'informations -->
                    <div class="row">
                        <!-- Carte Animaux -->
                        <div class="col-md-3">
                            <a href="gestionAnimaux.php" class="text-decoration-none">
                                <div class="card card-custom bg-purple text-white mb-4">
                                    <div class="card-body d-flex align-items-center">
                                        <i class="bi bi-gitlab"></i>
                                        <div>
                                            <h5 class="card-title">Animaux</h5>
                                            <p class="card-text"><?= $total_animaux ?> animaux</p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Carte Espèces -->
                        <div class="col-md-3">
                            <a href="gestionEspece.php" class="text-decoration-none">
                                <div class="card card-custom bg-warning text-dark mb-4">
                                    <div class="card-body d-flex align-items-center">
                                        <i class="bi bi-balloon-heart"></i>
                                        <div>
                                            <h5 class="card-title">Espèces</h5>
                                            <p class="card-text"><?= $total_especes ?> espèces</p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Carte Cages -->
                        <div class="col-md-3">
                            <a href="gestionCages.php" class="text-decoration-none">
                                <div class="card card-custom bg-danger text-white mb-4">
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
                            </a>
                        </div>

                        <!-- Carte Employés -->
                        <div class="col-md-3">
                            <a href="gestionPersonnel.php" class="text-decoration-none">
                                <div class="card card-custom bg-primary text-white mb-4">
                                    <div class="card-body d-flex align-items-center">
                                        <i class="bi bi-people-fill"></i>
                                        <div>
                                            <h5 class="card-title">Personnel</h5>
                                            <p class="card-text"><?= $total_employes ?> employés</p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Section Diagramme -->
                    <div class="row mt-4">
                        <div class="col-md-12 h-75"> <!-- Changer la hauteur à 50% -->
                            <div class="card card-custom h-75"> <!-- Définir une hauteur fixe -->
                                <div class="card-header bg-secondary text-white">
                                    <h5 class="card-title">Répartition des espèces</h5>
                                </div>
                                <div class="card-body">
                                    <!-- Boutons pour changer le type de graphique -->
                                    <div class="d-flex justify-content-center mb-3">
                                        <button onclick="changeChartType('pie')" class="btn btn-sm btn-primary me-2">Camembert</button>
                                        <button onclick="changeChartType('bar')" class="btn btn-sm btn-primary">Histogramme</button>
                                    </div>
                                    <!-- Conteneur du graphique -->
                                    <div class="chart-container mx-auto" style="max-width: 400px;"> <!-- Définir une largeur maximale -->
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
        let especesChart;

        // Données principales
        const mainLabels = <?= json_encode($labels) ?>;
        const mainData = <?= json_encode($data) ?>;
        const mainColors = <?= json_encode($colors) ?>;

        // Fonction pour créer le graphique
        function createChart(type) {
            if (especesChart) {
                especesChart.destroy();
            }
            especesChart = new Chart(ctx, {
                type: type,
                data: {
                    labels: mainLabels,
                    datasets: [{
                        data: mainData,
                        backgroundColor: mainColors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: type === 'pie', // Masquer la légende pour l'histogramme
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
        }

        // Fonction pour changer le type de graphique
        function changeChartType(type) {
            createChart(type);
        }

        // Initialiser le graphique en camembert par défaut
        createChart('pie');
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>