<?php
session_start();
require '../auth/initDb.php';

// Activer les erreurs PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur'])) {
    header('Location: ../index.php');
    exit;
}

// Gérer la suppression d'un animal
if (isset($_GET['delete'])) {
    $animalId = $_GET['delete'];

    // Vérifier que l'ID est valide
    if (!is_numeric($animalId)) {
        $_SESSION['error'] = "ID d'animal invalide.";
        header('Location: gestionAnimaux.php');
        exit;
    }

    try {
        // Activer les exceptions PDO
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Commencer une transaction
        $pdo->beginTransaction();

        // Supprimer les entrées liées dans la table animal_espece
        $stmt = $pdo->prepare("DELETE FROM animal_espece WHERE id_animal = ?");
        $stmt->execute([$animalId]);

        // Supprimer les entrées liées dans la table enfanter
        $stmt = $pdo->prepare("DELETE FROM enfanter WHERE id_animal_1 = ? OR id_animal = ?");
        $stmt->execute([$animalId, $animalId]);


        $stmt = $pdo->prepare("DELETE FROM s_occuper WHERE id_animal = ?");
        $stmt->execute([$animalId]);

        // Supprimer l'animal de la table animal
        $stmt = $pdo->prepare("DELETE FROM animal WHERE id_animal = ?");
        $stmt->execute([$animalId]);

        // Valider la transaction
        $pdo->commit();

        $_SESSION['success'] = "L'animal a été supprimé avec succès.";
    } catch (Exception $e) {
        // Annuler la transaction en cas d'erreur
        $pdo->rollBack();
        $_SESSION['error'] = "Erreur lors de la suppression de l'animal : " . $e->getMessage();
    }

    // Rediriger vers la même page pour éviter la resoumission du formulaire
    header('Location: gestionAnimaux.php');
    exit;
}
// Gestion de la recherche et du tri
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'nom';
$sort_order = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'DESC' : 'ASC';

// Gestion de la pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10; // Nombre d'animaux par page
$offset = ($page - 1) * $perPage;

// Sécurisation du tri pour éviter les injections SQL
$allowed_columns = ['nom', 'genre', 'numero', 'cage', 'espece'];
if (!in_array($sort_column, $allowed_columns)) {
    $sort_column = 'nom'; // Valeur par défaut si la colonne n'est pas autorisée
}

// Requête pour compter le nombre total d'animaux
$requete_count = $pdo->prepare("
    SELECT COUNT(DISTINCT a.id_animal) AS total
    FROM animal a
    LEFT JOIN cage c ON a.id_cage = c.id_cage
    INNER JOIN animal_espece ae ON a.id_animal = ae.id_animal
    INNER JOIN espece e ON ae.id_espece = e.id_espece
    WHERE a.nom LIKE :search OR a.genre LIKE :search OR a.numero LIKE :search OR a.pays LIKE :search OR e.nom LIKE :search
");
$requete_count->execute([':search' => "%$search%"]);
$total_animaux = $requete_count->fetch(PDO::FETCH_ASSOC)['total'];
$total_pages = ceil($total_animaux / $perPage);

// Requête pour récupérer les animaux de la page actuelle
$requete_animaux = $pdo->prepare("
    SELECT a.id_animal, a.nom, a.genre, a.numero, a.pays, a.date_naissance, a.date_arrivee, a.historique, a.image, c.numero AS cage, GROUP_CONCAT(e.nom SEPARATOR ', ') AS espece
    FROM animal a
    LEFT JOIN cage c ON a.id_cage = c.id_cage
    INNER JOIN animal_espece ae ON a.id_animal = ae.id_animal
    INNER JOIN espece e ON ae.id_espece = e.id_espece
    WHERE a.nom LIKE :search OR a.genre LIKE :search OR a.numero LIKE :search OR a.pays LIKE :search OR e.nom LIKE :search
    GROUP BY a.id_animal
    ORDER BY $sort_column $sort_order
    LIMIT :limit OFFSET :offset
");
// Liaison des paramètres avec PDO::PARAM_INT pour LIMIT et OFFSET
$requete_animaux->bindValue(':search', "%$search%", PDO::PARAM_STR);
$requete_animaux->bindValue(':limit', $perPage, PDO::PARAM_INT);
$requete_animaux->bindValue(':offset', $offset, PDO::PARAM_INT);

// Exécution de la requête
$requete_animaux->execute();

$animaux = $requete_animaux->fetchAll(PDO::FETCH_ASSOC);

// Fonction pour générer les liens de tri avec flèches
function getSortLink($column, $current_sort, $current_order)
{
    $new_order = ($current_sort === $column && $current_order === 'ASC') ? 'desc' : 'asc';
    $arrow = '';
    if ($current_sort === $column) {
        $arrow = $current_order === 'ASC' ? ' ↑' : ' ↓';
    }
    return "?sort=$column&order=$new_order&search=" . urlencode($_GET['search'] ?? '') . "&page=" . ($_GET['page'] ?? 1);
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
    <!-- AdminLTE CSS -->
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
                                                    <button type="submit" class="btn btn-primary border-white">Rechercher</button>
                                                </div>
                                                <div class="col-auto">
                                                    <!-- Bouton pour annuler les filtres -->
                                                    <a href="?" class="btn btn-danger">×</a>
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
                                                    <th class="<?= $sort_column === 'nom' ? 'sorted' : '' ?>">
                                                        <a href="<?= getSortLink('nom', $sort_column, $sort_order) ?>" class="text-white d-flex align-items-center">
                                                            Nom
                                                            <span class="sort-icon">
                                                                <?= $sort_column === 'nom' ? ($sort_order === 'ASC' ? '▲' : '▼') : '↕' ?>
                                                            </span>
                                                        </a>
                                                    </th>
                                                    <th class="<?= $sort_column === 'genre' ? 'sorted' : '' ?>">
                                                        <a href="<?= getSortLink('genre', $sort_column, $sort_order) ?>" class="text-white d-flex align-items-center">
                                                            Genre
                                                            <span class="sort-icon">
                                                                <?= $sort_column === 'genre' ? ($sort_order === 'ASC' ? '▲' : '▼') : '↕' ?>
                                                            </span>
                                                        </a>
                                                    </th>
                                                    <th class="<?= $sort_column === 'numero' ? 'sorted' : '' ?>">
                                                        <a href="<?= getSortLink('numero', $sort_column, $sort_order) ?>" class="text-white d-flex align-items-center">
                                                            Numéro
                                                            <span class="sort-icon">
                                                                <?= $sort_column === 'numero' ? ($sort_order === 'ASC' ? '▲' : '▼') : '↕' ?>
                                                            </span>
                                                        </a>
                                                    </th>
                                                    <th class="<?= $sort_column === 'cage' ? 'sorted' : '' ?>">
                                                        <a href="<?= getSortLink('cage', $sort_column, $sort_order) ?>" class="text-white d-flex align-items-center">
                                                            Cage
                                                            <span class="sort-icon">
                                                                <?= $sort_column === 'cage' ? ($sort_order === 'ASC' ? '▲' : '▼') : '↕' ?>
                                                            </span>
                                                        </a>
                                                    </th>
                                                    <th class="<?= $sort_column === 'espece' ? 'sorted' : '' ?>">
                                                        <a href="<?= getSortLink('espece', $sort_column, $sort_order) ?>" class="text-white d-flex align-items-center">
                                                            Espèce
                                                            <span class="sort-icon">
                                                                <?= $sort_column === 'espece' ? ($sort_order === 'ASC' ? '▲' : '▼') : '↕' ?>
                                                            </span>
                                                        </a>
                                                    </th>
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
                                                            <?php if ($_SESSION['utilisateur']['poste'] !== 'soigneur') : ?>
                                                                <a href="?delete=<?= $animal['id_animal'] ?>" class="btn btn-sm btn-danger"
                                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet animal ?');">Supprimer</a>
                                                            <?php endif; ?>
                                                        </td>

                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Pagination -->
                                    <nav aria-label="Page navigation">
                                        <ul class="pagination justify-content-center">
                                            <?php if ($page > 1) : ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="?page=<?= $page - 1 ?>&sort=<?= $sort_column ?>&order=<?= $sort_order ?>&search=<?= urlencode($search) ?>" aria-label="Previous">
                                                        <span aria-hidden="true">&laquo;</span>
                                                    </a>
                                                </li>
                                            <?php endif; ?>

                                            <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                                                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                                    <a class="page-link" href="?page=<?= $i ?>&sort=<?= $sort_column ?>&order=<?= $sort_order ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                                                </li>
                                            <?php endfor; ?>

                                            <?php if ($page < $total_pages) : ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="?page=<?= $page + 1 ?>&sort=<?= $sort_column ?>&order=<?= $sort_order ?>&search=<?= urlencode($search) ?>" aria-label="Next">
                                                        <span aria-hidden="true">&raquo;</span>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </nav>
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
    <!-- AdminLTE JS -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
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