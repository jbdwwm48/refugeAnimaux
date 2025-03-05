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
    <title>Gestion des Cages - Zoo Management</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        .navbar-custom {
            background-color: rgb(72, 149, 182);
            color: white;
        }

        .card-cage .card-header {
            padding: 0.5rem 1rem;
        }

        .card-cage .card-body {
            padding: 1rem;
        }

        .status-occupied {
            background-color: #28a745 !important;
            color: white;
        }

        .status-vacant {
            background-color: #007bff !important;
            color: white;
        }

        .card-cage {
            transition: all 0.3s;
        }

        .card-cage:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
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
                    <h1 class="text-center my-4">Gestion des Cages</h1>
                </div>
            </div>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <?php foreach ($cages as $cage): ?>
                            <div class="col-md-3 col-sm-6 col-12">
                                <div class="card card-cage">
                                    <div class="card-header <?php echo $cage['id_animal'] ? 'status-occupied' : 'status-vacant'; ?>">
                                        <h3 class="card-title">
                                            <i class="fas fa-home mr-1"></i>
                                            Cage <?= htmlspecialchars($cage['numero']) ?>
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <?php if ($cage['id_animal']): ?>
                                            <p class="mb-1">
                                                <i class="fas fa-paw mr-1"></i>
                                                <?= htmlspecialchars($cage['nom_animal']) ?>
                                            </p>
                                            <p class="mb-1">
                                                <i class="fas fa-id-badge mr-1"></i>
                                                ID: <?= htmlspecialchars($cage['id_animal']) ?>
                                            </p>
                                            <p class="mb-1">
                                                <i class="fas fa-leaf mr-1"></i>
                                                Espèce: <?= htmlspecialchars($cage['espece']) ?>
                                            </p>
                                        <?php else: ?>
                                            <p class="mb-1">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Statut: Libre
                                            </p>
                                        <?php endif; ?>
                                        <p class="mb-1">
                                            <i class="fas fa-map-marker-alt mr-1"></i>
                                            Allée: <?= htmlspecialchars($cage['allee']) ?>
                                        </p>
                                        <p class="mb-0">
                                            <i class="fas fa-building mr-1"></i>
                                            Salle: <?= htmlspecialchars($cage['salle']) ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE JS -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>

</html>