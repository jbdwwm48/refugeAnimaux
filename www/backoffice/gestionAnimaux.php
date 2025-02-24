<?php
session_start();
require '../auth/initDb.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur'])) {
    header('Location: ../index.php');
    exit;
}

// Gestion de la recherche et du tri
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'nom';
$sort_order = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'DESC' : 'ASC';

$requete_animaux = $pdo->prepare("
    SELECT a.id_animal, a.nom, a.genre, a.numero, a.pays, a.date_naissance, a.date_arrivee, a.historique, a.image, c.numero AS cage, e.nom AS espece
    FROM animal a
    LEFT JOIN cage c ON a.id_cage = c.id_cage
    INNER JOIN animal_espece ae ON a.id_animal = ae.id_animal
    INNER JOIN espece e ON ae.id_espece = e.id_espece
    WHERE a.nom LIKE ? OR a.genre LIKE ? OR a.numero LIKE ? OR a.pays LIKE ? OR e.nom LIKE ?
    ORDER BY $sort_column $sort_order
");
$requete_animaux->execute(["%$search%", "%$search%", "%$search%", "%$search%", "%$search%"]);
$animaux = $requete_animaux->fetchAll(PDO::FETCH_ASSOC);

// Fonction pour générer les liens de tri
function getSortLink($column, $current_sort, $current_order)
{
    $new_order = ($current_sort === $column && $current_order === 'ASC') ? 'desc' : 'asc';
    return "?sort=$column&order=$new_order&search=" . htmlspecialchars($_GET['search'] ?? '');
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Animaux</title>

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

        /* Style pour la lightbox */
        .lightbox {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .lightbox-content {
            background: white;
            padding: 20px;
            border-radius: 5px;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            position: relative;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 1.5rem;
            cursor: pointer;
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
                    <h1 class="text-center my-4">Gestion des Animaux</h1>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <!-- Tableau -->
                    <div class="row justify-content-center">
                        <div class="col-md-10">
                            <div class="card shadow">
                                <div class="card-header bg-primary text-white">
                                    <h2 class="card-title">Liste des Animaux</h2>
                                    <!-- Formulaire de recherche -->
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
                                                    <th><a href="<?= getSortLink('nom', $sort_column, $sort_order) ?>" class="text-white">Nom</a></th>
                                                    <th><a href="<?= getSortLink('genre', $sort_column, $sort_order) ?>" class="text-white">Genre</a></th>
                                                    <th><a href="<?= getSortLink('numero', $sort_column, $sort_order) ?>" class="text-white">Numéro</a></th>
                                                    <th><a href="<?= getSortLink('cage', $sort_column, $sort_order) ?>" class="text-white">Cage</a></th>
                                                    <th><a href="<?= getSortLink('espece', $sort_column, $sort_order) ?>" class="text-white">Espèce</a></th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($animaux as $animal) : ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($animal['nom']) ?></td>
                                                        <td><?= htmlspecialchars($animal['genre']) ?></td>
                                                        <td><?= htmlspecialchars($animal['numero']) ?></td>
                                                        <td><?= htmlspecialchars($animal['cage'] ?? 'Non assignée') ?></td>
                                                        <td><?= htmlspecialchars($animal['espece']) ?></td>
                                                        <td>
                                                            <button class="btn btn-sm btn-info show-animal" data-id="<?= $animal['id_animal'] ?>">Voir</button>
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

    <!-- Lightbox pour les détails de l'animal -->
    <div class="lightbox" id="animal-lightbox">
        <div class="lightbox-content">
            <span class="close-btn">&times;</span>
            <h3>Détails de l'animal</h3>
            <div id="animal-details"></div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- JavaScript pour gérer la lightbox -->
    <script>
        document.querySelectorAll('.show-animal').forEach(button => {
            button.addEventListener('click', function() {
                const animalId = this.getAttribute('data-id');
                const animals = <?php echo json_encode($animaux); ?>;
                const animal = animals.find(a => a.id_animal == animalId);

                if (animal) {
                    const details = `
                        <table class="table table-sm table-bordered">
                            <tr><th>Nom</th><td>${animal.nom}</td></tr>
                            <tr><th>Genre</th><td>${animal.genre}</td></tr>
                            <tr><th>Numéro</th><td>${animal.numero}</td></tr>
                            <tr><th>Pays</th><td>${animal.pays || 'N/A'}</td></tr>
                            <tr><th>Date de naissance</th><td>${animal.date_naissance || 'N/A'}</td></tr>
                            <tr><th>Date d'arrivée</th><td>${animal.date_arrivee || 'N/A'}</td></tr>
                            <tr><th>Cage</th><td>${animal.cage || 'Non assignée'}</td></tr>
                            <tr><th>Espèce</th><td>${animal.espece}</td></tr>
                            <tr><th>Historique</th><td>${animal.historique || 'Aucun'}</td></tr>
                            ${animal.image ? `<tr><th>Image</th><td><img src="${animal.image}" alt="${animal.nom}" style="max-width: 200px;"></td></tr>` : ''}
                        </table>
                    `;
                    document.getElementById('animal-details').innerHTML = details;
                    document.getElementById('animal-lightbox').style.display = 'flex';
                }
            });
        });

        document.querySelector('.close-btn').addEventListener('click', function() {
            document.getElementById('animal-lightbox').style.display = 'none';
        });

        document.getElementById('animal-lightbox').addEventListener('click', function(e) {
            if (e.target === this) {
                this.style.display = 'none';
            }
        });
    </script>
</body>

</html>