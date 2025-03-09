<?php
// Démarre une session PHP
session_start();
// Renouvelle l'ID de session pour des raisons de sécurité
session_regenerate_id(true);

// Inclusion du fichier de connexion à la base de données
require '../auth/initDb.php';

// Activer l'affichage des erreurs PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Vérifie si l'utilisateur est connecté, sinon redirige vers la page d'accueil
if (!isset($_SESSION['utilisateur'])) {
    header('Location: ../index.php');
    exit;
}

// Gestion de la suppression d'un animal
if (isset($_GET['delete'])) {
    $animalId = $_GET['delete'];

    // Vérifie que l'ID de l'animal est un nombre valide
    if (!is_numeric($animalId)) {
        $_SESSION['error'] = "ID d'animal invalide.";
        header('Location: gestionAnimaux.php');
        exit;
    }

    try {
        // Active les exceptions PDO pour gérer les erreurs SQL

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // Commence une transaction pour que toutes les suppressions se fassent en une seule fois
        $pdo->beginTransaction();
        // Supprime les relations de l'animal dans la table animal_espece
        $stmt = $pdo->prepare("DELETE FROM animal_espece WHERE id_animal = ?");
        $stmt->execute([$animalId]);
        // Supprime les relations dans la table enfanter où l'animal est parent
        $stmt = $pdo->prepare("DELETE FROM enfanter WHERE id_animal = ? OR id_animal_1 = ?");
        $stmt->execute([$animalId, $animalId]);
        // Supprime les entrées dans la table s_occuper liées à l'animal
        $stmt = $pdo->prepare("DELETE FROM s_occuper WHERE id_animal = ?");
        $stmt->execute([$animalId]);
        // Supprime l'animal de la table principale 'animal'
        $stmt = $pdo->prepare("DELETE FROM animal WHERE id_animal = ?");
        $stmt->execute([$animalId]);
        // Si tout s'est bien passé, on valide la transaction
        $pdo->commit();
        $_SESSION['success'] = "L'animal a été supprimé avec succès.";
    } catch (Exception $e) {

        // Si une erreur survient, on annule les modifications
        $pdo->rollBack();
        $_SESSION['error'] = "Erreur lors de la suppression de l'animal : " . $e->getMessage();
    }


    // Redirige à la page de gestion des animaux
    header('Location: gestionAnimaux.php');
    exit;
}

// Gestion des filtres de recherche et de tri
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'nom';
$sort_order = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'DESC' : 'ASC';

// Gestion de la pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10; // Nombre d'animaux par page
$offset = ($page - 1) * $perPage;

// Securisation du tri des colonnes pour éviter les injections SQL
$allowed_columns = ['nom', 'genre', 'numero', 'cage', 'espece'];
if (!in_array($sort_column, $allowed_columns)) {
    $sort_column = 'nom'; // Par défaut, on trie par le nom
}

// Requête pour compter le nombre total d'animaux correspondant à la recherche
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
$total_pages = ceil($total_animaux / $perPage); // Calcul du nombre total de pages pour la pagination

// Requête pour récupérer les animaux selon la page, tri et recherche
$requete_animaux = $pdo->prepare("
    SELECT a.id_animal, a.nom, a.genre, a.numero, a.pays, a.date_naissance, a.date_arrivee, a.historique, a.image, c.numero AS cage, GROUP_CONCAT(e.nom SEPARATOR ', ') AS espece
    FROM animal a
    LEFT JOIN cage c ON a.id_cage = c.id_cage
    INNER JOIN animal_espece ae ON a.id_animal = ae.id_animal
    INNER JOIN espece e ON ae.id_espece = e.id_espece
    WHERE a.nom LIKE :search OR a.genre LIKE :search OR a.numero LIKE :search OR a.pays LIKE :search OR e.nom LIKE :search
    GROUP BY a.id_animal
    ORDER BY $sort_column $sort_order
    LIMIT $perPage OFFSET $offset
");

// Lier les valeurs pour la recherche et pagination
$requete_animaux->bindValue(':search', "%$search%", PDO::PARAM_STR);
$requete_animaux->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);
$requete_animaux->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

// Exécution de la requête pour récupérer les animaux
$requete_animaux->execute();
$animaux = $requete_animaux->fetchAll(PDO::FETCH_ASSOC);

// Fonction pour formater la date
function formatDate($date)
{
    if (!$date) return 'N/A';
    $dateTime = DateTime::createFromFormat('Y-m-d', $date);
    return $dateTime ? $dateTime->format('d/m/Y') : 'N/A';
}

// Récupérer les cages libres
$sql_cages_libres = "
    SELECT c.* 
    FROM cage c 
    LEFT JOIN animal a ON c.id_cage = a.id_cage 
    WHERE a.id_cage IS NULL
";
$stmt_cages_libres = $pdo->prepare($sql_cages_libres);
$stmt_cages_libres->execute();
$cages_libres = $stmt_cages_libres->fetchAll();

// Formatage des dates pour chaque animal
foreach ($animaux as &$animal) {
    $animal['date_naissance'] = formatDate($animal['date_naissance'] ?? null);
    $animal['date_arrivee'] = formatDate($animal['date_arrivee'] ?? null);
}
unset($animal);

// Fonction pour générer des liens de tri
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
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
            background-color: #19433e;
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
            <!-- Afficher un message de succès ou d'erreur -->
            <?php if (isset($_SESSION['success'])) : ?>
                <div class="alert alert-success m-4" role="alert"><?= $_SESSION['success'] ?></div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])) : ?>
                <div class="alert alert-danger m-4" role="alert"><?= $_SESSION['error'] ?></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

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
                                        <div class="d-flex justify-content-end gap-4">

                                            <!-- Bouton pour ouvrir le modal -->
                                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#ajouterAnimalModal" aria-label="Ajouter un animal">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
                                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4" />
                                                </svg>
                                                Ajouter un animal
                                            </button>

                                            <form method="GET" class="row g-3">
                                                <div class="col-auto">
                                                    <input type="text" name="search" class="form-control" placeholder="Rechercher..." value="<?= htmlspecialchars($search) ?>">
                                                </div>
                                                <div class="col-auto">
                                                    <button type="submit" class="btn btn-primary border-white" aria-label="Rechercher un animal">Rechercher</button>
                                                </div>
                                                <div class="col-auto">
                                                    <!-- Bouton pour annuler les filtres -->
                                                    <a href="GestionAnimaux.php" class="btn btn-danger">×</a>
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
                                                            <button class="btn btn-sm btn-info show-animal" aria-label="Voir le detail d'un animal" data-id="<?= $animal['id_animal'] ?>"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                                                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z" />
                                                                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0" />
                                                                </svg></button>
                                                            <?php if ($_SESSION['utilisateur']['poste'] !== 'soigneur') : ?>
                                                                <a href="update_animal.php?id=<?= $animal['id_animal'] ?>" class="btn btn-sm btn-warning" aria-label="Modifier un animal">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                                                        <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-9.5 9.5a.5.5 0 0 1-.168.11l-4 1a.5.5 0 0 1-.62-.62l1-4a.5.5 0 0 1 .11-.168l9.5-9.5zM11.207 2L3 10.207V12h1.793L14 3.793 11.207 2zM2 13h10v1H2v-1z" />
                                                                    </svg>
                                                                </a>
                                                            <?php endif; ?>
                                                            <?php if ($_SESSION['utilisateur']['poste'] !== 'soigneur') : ?>
                                                                <button type="button" class="btn btn-sm btn-danger delete-animal" aria-label="Supprimer un animal" data-id="<?= $animal['id_animal'] ?>">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z" />
                                                                        <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z" />
                                                                    </svg>
                                                                </button>
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
            <span class="close-btn" aria-label="fermer la fenetre">&times;</span>
            <h3>Détails de l'animal</h3>
            <div id="animal-details"></div>
        </div>
    </div>

    <!-- Modal de confirmation de suppression -->
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirmation de suppression</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Êtes-vous sûr de vouloir supprimer l'animal : <strong id="animalName"></strong> ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <a id="confirmDeleteButton" href="#" class="btn btn-danger">Supprimer</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pour ajouter un animal -->
    <div class="modal fade" id="ajouterAnimalModal" tabindex="-1" aria-labelledby="ajouterAnimalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ajouterAnimalModalLabel">Ajouter un animal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close fermer la fenetre"></button>
                </div>
                <div class="modal-body">
                    <form id="formAjouterAnimal" action="ajouter_animal.php" method="POST">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom</label>
                            <input type="text" class="form-control" id="nom" name="nom" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Genre</label>
                            <div>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" id="genreF" name="genre" value="F" required checked>
                                    <label class="form-check-label" for="genreF">Femelle</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" id="genreM" name="genre" value="M" required>
                                    <label class="form-check-label" for="genreM">Mâle</label>
                                </div>

                            </div>
                        </div>

                        <div class="form-group">
                            <label for="numero">Numéro</label>
                            <input type="text" class="form-control" name="numero" id="numero">
                        </div>

                        <div class="mb-3">
                            <label for="pays" class="form-label">Pays d'origine</label>
                            <input type="text" class="form-control" id="pays" name="pays">
                        </div>

                        <div class="mb-3">
                            <label for="date_naissance" class="form-label">Date de naissance</label>
                            <input type="date" class="form-control" id="date_naissance" name="date_naissance">
                        </div>

                        <div class="mb-3">
                            <label for="date_arrivee" class="form-label">Date d'arrivée</label>
                            <input type="date" class="form-control" id="date_arrivee" name="date_arrivee">
                        </div>

                        <div class="mb-3">
                            <label for="historique" class="form-label">Historique</label>
                            <textarea class="form-control" id="historique" name="historique" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">URL de l'image</label>
                            <input type="url" class="form-control" id="image" name="image" placeholder="https://exemple.com/image.jpg" value="">
                            <img id="imagePreview" src="<?= htmlspecialchars($animal['image']) ?>" alt="Aperçu" class="mt-2" style="max-width: 200px; display: <?= !empty($animal['image']) ? 'block' : 'none' ?>;">
                        </div>

                        <div class="form-group">
                            <label for="cage">Cage</label>
                            <select class="form-control" name="cage" id="cage" required>
                                <option value="">Sélectionner une cage</option>
                                <?php foreach ($cages_libres as $cage) : ?>
                                    <option value="<?= $cage['id_cage'] ?>">Cage #<?= htmlspecialchars($cage['id_cage']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <?php
                        // Récupérer toutes les espèces depuis la base de données
                        $sql = "SELECT id_espece, nom FROM espece";
                        $especes = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
                        ?>


                        <!-- Espèce -->
                        <div class="form-group">
                            <label for="espece">Espèce</label>
                            <select class="form-control" name="espece" id="espece" required>
                                <option value="">Sélectionner une espèce</option>
                                <?php foreach ($especes as $espece) : ?>
                                    <option value="<?= $espece['id_espece'] ?>">
                                        <?= htmlspecialchars($espece['nom']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" form="formAjouterAnimal" class="btn btn-primary" aria-label="Ajouter cet animal">Ajouter</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE JS -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

    <!-- JavaScript pour gérer la lightbox -->
    <script>
        document.getElementById('image').addEventListener('input', function() {
            const imageUrl = this.value;
            const imagePreview = document.getElementById('imagePreview');
            if (imageUrl) {
                imagePreview.src = imageUrl;
                imagePreview.style.display = 'block';
            } else {
                imagePreview.style.display = 'none';
            }
        });

        // Quand la page est chargée
        document.addEventListener('DOMContentLoaded', function() {
            // Gérer l'ouverture de la lightbox
            document.querySelectorAll('.show-animal').forEach(button => {
                button.addEventListener('click', function() {
                    console.log('Click détecté');
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
                        <tr><th>Date de naissance</th><td>${animal.date_naissance}</td></tr>
                        <tr><th>Date d'arrivée</th><td>${animal.date_arrivee}</td></tr>
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

            // Gérer la fermeture de la lightbox
            const closeBtn = document.querySelector('.close-btn');
            const lightbox = document.getElementById('animal-lightbox');

            if (closeBtn && lightbox) {
                closeBtn.addEventListener('click', function() {
                    lightbox.style.display = 'none';
                });

                lightbox.addEventListener('click', function(e) {
                    if (e.target === this) {
                        this.style.display = 'none';
                    }
                });
            }
        });


        document.addEventListener('DOMContentLoaded', function() {
            // Gérer l'ouverture du modal de suppression
            document.querySelectorAll('.delete-animal').forEach(button => {
                button.addEventListener('click', function() {
                    const animalId = this.getAttribute('data-id');
                    const animals = <?php echo json_encode($animaux); ?>;
                    const animal = animals.find(a => a.id_animal == animalId);

                    if (animal) {
                        const deleteUrl = `?delete=${animalId}`;

                        // Mettre à jour le lien de suppression dans le modal
                        document.getElementById('confirmDeleteButton').href = deleteUrl;

                        // Afficher le nom de l'animal dans le modal
                        document.getElementById('animalName').textContent = animal.nom;
                        // Ouvrir le modal
                        const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
                        deleteModal.show();
                    }
                });
            });
        });

        document.getElementById('formAjouterAnimal').addEventListener('submit', function() {
            // Fermer le modal après la soumission du formulaire
            const modal = bootstrap.Modal.getInstance(document.getElementById('ajouterAnimalModal'));
            modal.hide();
        });
    </script>
</body>

</html>