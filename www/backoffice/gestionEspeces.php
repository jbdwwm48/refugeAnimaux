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

// Gestion de la suppression d'une espèce
if (isset($_GET['delete'])) {
    $especeId = $_GET['delete'];

    // Vérifie que l'ID de l'espèce est un nombre valide
    if (!is_numeric($especeId)) {
        $_SESSION['error'] = "ID d'espèce invalide.";
        header('Location: gestionEspeces.php');
        exit;
    }

    try {
        // Active les exceptions PDO pour gérer les erreurs SQL
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Étape 1 : Vérifier s'il y a des animaux associés à cette espèce
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM animal_espece WHERE id_espece = ?");
        $stmt->execute([$especeId]);
        $animauxCount = $stmt->fetchColumn();

        if ($animauxCount > 0) {
            // Si des animaux sont encore présents, afficher une alerte
            $_SESSION['error'] = "Des animaux appartenant à l'espèce sont encore présents dans le refuge.";
            header('Location: gestionEspeces.php');
            exit;
        }

        // Commence une transaction pour que toutes les suppressions se fassent en une seule fois
        $pdo->beginTransaction();

        // Étape 2 : Supprimer les relations dans la table animal_espece
        $stmt = $pdo->prepare("DELETE FROM animal_espece WHERE id_espece = ?");
        $stmt->execute([$especeId]);

        // Étape 3 : Supprimer l'espèce
        $stmt = $pdo->prepare("DELETE FROM espece WHERE id_espece = ?");
        $stmt->execute([$especeId]);

        // Si tout s'est bien passé, on valide la transaction
        $pdo->commit();
        $_SESSION['success'] = "L'espèce a été supprimée avec succès.";
        header('Location: gestionEspeces.php');
        exit;
    } catch (Exception $e) {
        // Si une erreur survient, on annule les modifications
        $pdo->rollBack();
        $_SESSION['error'] = "Erreur lors de la suppression de l'espèce : " . $e->getMessage();
        header('Location: gestionEspeces.php');
        exit;
    }
}

// Gestion des filtres de recherche et de tri
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'nom';
$sort_order = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'DESC' : 'ASC';

// Gestion de la pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10; // Nombre d'espèces par page
$offset = ($page - 1) * $perPage;

// Sécurisation du tri des colonnes pour éviter les injections SQL
$allowed_columns = ['nom', 'nombre_animaux'];
if (!in_array($sort_column, $allowed_columns)) {
    $sort_column = 'nom'; // Par défaut, on trie par le nom
}

// Requête pour compter le nombre total d'espèces correspondant à la recherche
$requete_count = $pdo->prepare("
    SELECT COUNT(DISTINCT e.id_espece) AS total
    FROM espece e
    LEFT JOIN animal_espece ae ON e.id_espece = ae.id_espece
    WHERE e.nom LIKE :search
");
$requete_count->execute([':search' => "%$search%"]);
$total_especes = $requete_count->fetch(PDO::FETCH_ASSOC)['total'];
$total_pages = ceil($total_especes / $perPage); // Calcul du nombre total de pages pour la pagination

// Requête pour récupérer les espèces selon la page, tri et recherche
$requete_especes = $pdo->prepare("
    SELECT e.id_espece, e.nom, COUNT(ae.id_animal) AS nombre_animaux
    FROM espece e
    LEFT JOIN animal_espece ae ON e.id_espece = ae.id_espece
    WHERE e.nom LIKE :search
    GROUP BY e.id_espece
    ORDER BY $sort_column $sort_order
    LIMIT :limit OFFSET :offset
");

// Lier les valeurs pour la recherche et pagination
$requete_especes->bindValue(':search', "%$search%", PDO::PARAM_STR);
$requete_especes->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);
$requete_especes->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

// Exécution de la requête pour récupérer les espèces
$requete_especes->execute();
$especes = $requete_especes->fetchAll(PDO::FETCH_ASSOC);

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
    <title>Gestion des Espèces</title>
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
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                    <h1 class="text-center my-4">Gestion des Espèces</h1>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <!-- Affichage des messages -->
                    <?php if (isset($_SESSION['success'])) : ?>
                        <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['error'])) : ?>
                        <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <!-- Tableau -->
                    <div class="row justify-content-center">
                        <div class="col-md-10">
                            <div class="card shadow">
                                <div class="card-header bg-primary text-white">
                                    <h2 class="card-title">Liste des Espèces</h2>
                                    <!-- Formulaire de recherche -->
                                    <div class="container-fluid mb-1">
                                        <div class="d-flex justify-content-end gap-4">
                                            <div class="row justify-content-end">
                                                <div class="col-auto">
                                                    <button class="btn btn-success " data-bs-toggle="modal" data-bs-target="#addEspeceModal">
                                                        <i class="fas fa-plus"></i> Ajouter une espèce
                                                    </button>
                                                </div>
                                            </div>
                                            <form method="GET" class="row g-3">
                                                <div class="col-auto">
                                                    <input type="text" name="search" class="form-control" placeholder="Rechercher..." value="<?= htmlspecialchars($search) ?>">
                                                </div>
                                                <div class="col-auto">
                                                    <button type="submit" class="btn btn-primary border-white">Rechercher</button>
                                                </div>
                                                <div class="col-auto">
                                                    <!-- Bouton pour annuler les filtres -->
                                                    <a href="gestionEspeces.php" class="btn btn-danger">×</a>
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
                                                    <th class="<?= $sort_column === 'nombre_animaux' ? 'sorted' : '' ?>">
                                                        <a href="<?= getSortLink('nombre_animaux', $sort_column, $sort_order) ?>" class="text-white d-flex align-items-center">
                                                            Nombre d'animaux
                                                            <span class="sort-icon">
                                                                <?= $sort_column === 'nombre_animaux' ? ($sort_order === 'ASC' ? '▲' : '▼') : '↕' ?>
                                                            </span>
                                                        </a>
                                                    </th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($especes as $espece) : ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($espece['nom']) ?></td>
                                                        <td><?= htmlspecialchars($espece['nombre_animaux']) ?></td>
                                                        <td>
                                                            <?php if ($_SESSION['utilisateur']['poste'] !== 'soigneur') : ?>
                                                                <!-- Bouton Modifier -->
                                                                <button type="button" class="btn btn-sm btn-warning edit-espece" data-bs-toggle="modal" data-bs-target="#editEspeceModal" data-id="<?= $espece['id_espece'] ?>" data-nom="<?= htmlspecialchars($espece['nom']) ?>">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                                <!-- Bouton Supprimer -->
                                                                <?php if ($espece['nombre_animaux'] == 0) : ?>
                                                                    <button type="button" class="btn btn-sm btn-danger delete-espece" data-id="<?= $espece['id_espece'] ?>" data-nom="<?= htmlspecialchars($espece['nom']) ?>">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                <?php endif; ?>
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

    <!-- Modal pour ajouter une espèce -->
    <div class="modal fade" id="addEspeceModal" tabindex="-1" aria-labelledby="addEspeceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addEspeceModalLabel">Ajouter une espèce</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Formulaire d'ajout d'espèce -->
                    <form action="ajouter_espece.php" method="POST">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom de l'espèce</label>
                            <input type="text" class="form-control" id="nom" name="nom" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Ajouter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pour modifier une espèce -->
    <div class="modal fade" id="editEspeceModal" tabindex="-1" aria-labelledby="editEspeceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editEspeceModalLabel">Modifier une espèce</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Formulaire de modification -->
                    <form action="update_espece.php" method="POST">
                        <input type="hidden" id="edit_id_espece" name="id_espece">
                        <div class="mb-3">
                            <label for="edit_nom" class="form-label">Nom de l'espèce</label>
                            <input type="text" class="form-control" id="edit_nom" name="nom" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
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
                    Êtes-vous sûr de vouloir supprimer l'espèce : <strong id="especeNom"></strong> ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <a id="confirmDeleteButton" href="#" class="btn btn-danger">Supprimer</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE JS -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    <!-- JavaScript pour gérer la suppression et la modification -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gérer l'ouverture du modal de suppression
            document.querySelectorAll('.delete-espece').forEach(button => {
                button.addEventListener('click', function() {
                    const especeId = this.getAttribute('data-id');
                    const especeNom = this.getAttribute('data-nom');
                    const deleteUrl = `?delete=${especeId}`;

                    // Mettre à jour le lien de suppression dans le modal
                    document.getElementById('confirmDeleteButton').href = deleteUrl;

                    // Afficher le nom de l'espèce dans le modal
                    document.getElementById('especeNom').textContent = especeNom;
                    // Ouvrir le modal
                    const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
                    deleteModal.show();
                });
            });

            // Gérer l'ouverture du modal de modification
            document.querySelectorAll('.edit-espece').forEach(button => {
                button.addEventListener('click', function() {
                    const especeId = this.getAttribute('data-id');
                    const especeNom = this.getAttribute('data-nom');

                    // Pré-remplir les champs du modal
                    document.getElementById('edit_id_espece').value = especeId;
                    document.getElementById('edit_nom').value = especeNom;
                });
            });
        });
    </script>
</body>

</html>