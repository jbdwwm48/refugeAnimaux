<?php
session_start();
require '../auth/initDb.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur'])) {
    header('Location: ../index.php');
    exit;
}

// Fonction pour exécuter une requête et retourner le résultat
function fetchData($pdo, $query, $params = [])
{
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Récupération des données
$total_animaux = fetchData($pdo, "SELECT COUNT(*) AS total_animaux FROM animal")['total_animaux'];

$cages_data = fetchData($pdo, "
    SELECT COUNT(*) AS total_cages, SUM(IF(a.id_animal IS NOT NULL, 1, 0)) AS cages_occupees 
    FROM cage c 
    LEFT JOIN animal a ON c.id_cage = a.id_cage
");
$total_cages = $cages_data['total_cages'];
$cages_occupees = $cages_data['cages_occupees'];
$taux_occupation = $total_cages > 0 ? round(($cages_occupees / $total_cages) * 100, 2) : 0;

$total_employes = fetchData($pdo, "SELECT COUNT(*) AS total_employes FROM personnel")['total_employes'];
$total_especes = fetchData($pdo, "SELECT COUNT(DISTINCT id_espece) AS total_especes FROM animal_espece")['total_especes'];

$especes_animaux = $pdo->query("
    SELECT e.nom AS espece, COUNT(a.id_animal) AS nombre_animaux 
    FROM animal a 
    INNER JOIN animal_espece ae ON a.id_animal = ae.id_animal 
    INNER JOIN espece e ON ae.id_espece = e.id_espece 
    GROUP BY e.nom
")->fetchAll(PDO::FETCH_ASSOC);

// Préparation des données pour le graphique
$labels = array_column($especes_animaux, 'espece');
$data = array_column($especes_animaux, 'nombre_animaux');
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Zoo Management</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .navbar-custom {
            background-color: rgb(72, 149, 182);
            color: white;
        }

        .small-box {
            border-radius: 0.25rem;
            box-shadow: 0 0 1px rgba(0, 0, 0, .125), 0 1px 3px rgba(0, 0, 0, .2);
        }

        .small-box:hover {
            transform: translateY(-5px);
            transition: all 0.3s;
        }

        .chart-container {
            position: relative;
            height: 400px;
            width: 100%;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini">
    <!-- Navbar -->
    <?php include('../nav.php'); ?>

    <div class="wrapper">
        <!-- Sidebar -->
        <?php include('./sidebar.php'); ?>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header -->
            <div class="content-header">
                <div class="container-fluid">
                    <h1 class="text-center my-4">Tableau de Bord</h1>
                </div>
            </div>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <!-- Small boxes -->
                    <div class="row">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3><?= $total_animaux ?></h3>
                                    <p>Animaux</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-paw"></i>
                                </div>
                                <a href="gestionAnimaux.php" class="small-box-footer">
                                    Plus d'infos <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>

                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3><?= $total_especes ?></h3>
                                    <p>Espèces</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-leaf"></i>
                                </div>
                                <a href="gestionEspeces.php" class="small-box-footer">
                                    Plus d'infos <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>

                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3><?= $taux_occupation ?><sup style="font-size: 20px">%</sup></h3>
                                    <p>Cages occupées</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-home"></i>
                                </div>
                                <a href="gestionCages.php" class="small-box-footer">
                                    Plus d'infos <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>

                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-primary">
                                <div class="inner">
                                    <h3><?= $total_employes ?></h3>
                                    <p>Employés</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <a href="gestionPersonnel.php" class="small-box-footer">
                                    Plus d'infos <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Chart -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow">
                                <div class="card-header bg-primary text-white">
                                    <h3 class="card-title">Répartition des Espèces</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool text-white" onclick="changeChartType('pie')">
                                            <i class="fas fa-chart-pie"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool text-white" onclick="changeChartType('bar')">
                                            <i class="fas fa-chart-bar"></i>
                                        </button>
                                    </div>
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE JS -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

    <!-- Chart.js Script -->
    <!-- Remplacez le script Chart.js existant par celui-ci -->
    <script>
        const ctx = document.getElementById('especesChart').getContext('2d');
        let especesChart;

        const mainLabels = <?= json_encode($labels) ?>;
        const mainData = <?= json_encode($data) ?>;

        // Définir les couleurs spécifiques par espèce
        const couleursEspeces = {
            'Chien': '#335c67',
            'Chat': '#FF9800',
            'Cheval': '#A0522D',
            'Girafe': '#FFD54F',
            'Éléphant': '#9E9E9E',
            'Serpent': '#2E7D32',
            'Crocodile': '#9C27B0',
            'Loup': '#344f64',
            'Âne': '#5d5737'
        };

        // Créer un tableau de couleurs correspondant aux labels
        const backgroundColors = mainLabels.map(label => couleursEspeces[label] || '#CCCCCC'); // Gris par défaut si espèce non trouvée
        const borderColors = backgroundColors.map(color => color); // Même couleur pour les bordures

        function createChart(type) {
            if (especesChart) especesChart.destroy();
            especesChart = new Chart(ctx, {
                type: type,
                data: {
                    labels: mainLabels,
                    datasets: [{
                        data: mainData,
                        backgroundColor: backgroundColors,
                        borderColor: borderColors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            display: type === 'pie'
                        }
                    },
                    scales: type === 'bar' ? {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Nombre d\'animaux'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Espèces'
                            }
                        }
                    } : {}
                }
            });
        }

        function changeChartType(type) {
            createChart(type);
        }

        // Initialiser avec le graphique en pie
        createChart('pie');
    </script>
</body>

</html>