<?php
session_start();
require '../auth/initDb.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur'])) {
    header('Location: ../index.php');
    exit;
}

// Récupérer les informations de l'utilisateur
$role_utilisateur = strtolower($_SESSION['utilisateur']['poste']); // Convertir en minuscules
$id_utilisateur = $_SESSION['utilisateur']['id_personnel'];
$prenom_utilisateur = $_SESSION['utilisateur']['prenom'];
$nom_utilisateur = $_SESSION['utilisateur']['nom'];

// Fonction pour filtrer les données en fonction du rôle
function filtrerDonneesParRole($pdo, $role_utilisateur, $id_utilisateur = null)
{
    if ($role_utilisateur === 'soigneur') {
        // Le soigneur ne voit que ses propres informations
        $requete_personnels = $pdo->prepare("
            SELECT id_personnel, nom, poste, login 
            FROM personnel 
            WHERE id_personnel = :id_utilisateur
        ");
        $requete_personnels->bindValue(':id_utilisateur', $id_utilisateur, PDO::PARAM_INT);
        $requete_personnels->execute();
        $personnel = $requete_personnels->fetch(PDO::FETCH_ASSOC);

        if (!$personnel) {
            return [];
        }

        // Récupérer les animaux associés au soigneur
        $id_personnel = $personnel['id_personnel'];
        $requete_animaux = $pdo->prepare("
            SELECT a.nom, a.genre, a.numero, a.pays, a.date_naissance, a.date_arrivee, a.date_deces, a.historique, a.image, e.nom as espece 
            FROM animal a 
            INNER JOIN s_occuper s ON a.id_animal = s.id_animal 
            INNER JOIN animal_espece ae ON a.id_animal = ae.id_animal 
            INNER JOIN espece e ON ae.id_espece = e.id_espece 
            WHERE s.id_personnel = :id_personnel
        ");
        $requete_animaux->bindValue(':id_personnel', $id_personnel, PDO::PARAM_INT);
        $requete_animaux->execute();
        $animaux = $requete_animaux->fetchAll(PDO::FETCH_ASSOC);

        $personnel['animaux'] = $animaux;
        return [$personnel]; // Retourner un tableau contenant le soigneur
    } elseif ($role_utilisateur === 'cadre') {
        // Le cadre voit tous les soigneurs et leurs animaux
        $requete_personnels = $pdo->prepare("
            SELECT id_personnel, nom, poste, login 
            FROM personnel 
            WHERE poste = 'soigneur'
        ");
        $requete_personnels->execute();
        $personnels = $requete_personnels->fetchAll(PDO::FETCH_ASSOC);

        // Pour chaque soigneur, récupérer les animaux associés
        foreach ($personnels as &$personnel) {
            $id_personnel = $personnel['id_personnel'];
            $requete_animaux = $pdo->prepare("
                SELECT a.nom, a.genre, a.numero, a.pays, a.date_naissance, a.date_arrivee, a.date_deces, a.historique, a.image, e.nom as espece 
                FROM animal a 
                INNER JOIN s_occuper s ON a.id_animal = s.id_animal 
                INNER JOIN animal_espece ae ON a.id_animal = ae.id_animal 
                INNER JOIN espece e ON ae.id_espece = e.id_espece 
                WHERE s.id_personnel = :id_personnel
            ");
            $requete_animaux->bindValue(':id_personnel', $id_personnel, PDO::PARAM_INT);
            $requete_animaux->execute();
            $animaux = $requete_animaux->fetchAll(PDO::FETCH_ASSOC);

            $personnel['animaux'] = $animaux;
        }

        return $personnels; // Retourner un tableau contenant tous les soigneurs et leurs animaux
    } elseif ($role_utilisateur === 'administratif') {
        // L'administratif voit tout le personnel
        $requete_personnels = $pdo->prepare("
            SELECT id_personnel, nom, poste, login 
            FROM personnel
        ");
        $requete_personnels->execute();
        $personnels = $requete_personnels->fetchAll(PDO::FETCH_ASSOC);

        // Pour chaque soigneur, récupérer les animaux associés
        foreach ($personnels as &$personnel) {
            if ($personnel['poste'] === 'soigneur') {
                $id_personnel = $personnel['id_personnel'];
                $requete_animaux = $pdo->prepare("
                    SELECT a.nom, a.genre, a.numero, a.pays, a.date_naissance, a.date_arrivee, a.date_deces, a.historique, a.image, e.nom as espece 
                    FROM animal a 
                    INNER JOIN s_occuper s ON a.id_animal = s.id_animal 
                    INNER JOIN animal_espece ae ON a.id_animal = ae.id_animal 
                    INNER JOIN espece e ON ae.id_espece = e.id_espece 
                    WHERE s.id_personnel = :id_personnel
                ");
                $requete_animaux->bindValue(':id_personnel', $id_personnel, PDO::PARAM_INT);
                $requete_animaux->execute();
                $animaux = $requete_animaux->fetchAll(PDO::FETCH_ASSOC);

                $personnel['animaux'] = $animaux;
            }
        }

        return $personnels; // Retourner un tableau contenant tout le personnel
    } else {
        // Si le rôle n'est pas reconnu, retourner un tableau vide
        return [];
    }
}

// Filtrer les données en fonction du rôle
$personnels = filtrerDonneesParRole($pdo, $role_utilisateur, $id_utilisateur);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion du Personnel</title>

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

        .animal-details table {
            margin-bottom: 0;
        }

        .collapse-toggle {
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
                                    <h2 class="card-title">Liste du Personnel</h2>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered table-striped">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>Nom</th>
                                                    <th>Poste</th>
                                                    <th>Login</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($personnels)) : ?>
                                                    <tr>
                                                        <td colspan="4" class="text-center">Aucun employé trouvé.</td>
                                                    </tr>
                                                <?php else : ?>
                                                    <?php foreach ($personnels as $personnel) : ?>
                                                        <tr>
                                                            <td><?= htmlspecialchars($personnel['nom']) ?></td>
                                                            <td><?= htmlspecialchars($personnel['poste']) ?></td>
                                                            <td><?= htmlspecialchars($personnel['login']) ?></td>
                                                            <td>
                                                                <?php if ($role_utilisateur === 'administratif') : ?>
                                                                    <button class="btn btn-sm btn-warning">Modifier</button>
                                                                    <button class="btn btn-sm btn-danger">Supprimer</button>
                                                                <?php endif; ?>
                                                                <?php if (($role_utilisateur === 'cadre' || $role_utilisateur === 'administratif') && $personnel['poste'] === 'soigneur' && !empty($personnel['animaux'])) : ?>
                                                                    <button class="btn btn-sm btn-info collapse-toggle" data-bs-toggle="collapse" data-bs-target="#animaux-<?= $personnel['id_personnel'] ?>">
                                                                        Voir les animaux
                                                                    </button>
                                                                <?php elseif ($role_utilisateur === 'soigneur' && !empty($personnel['animaux'])) : ?>
                                                                    <button class="btn btn-sm btn-info collapse-toggle" data-bs-toggle="collapse" data-bs-target="#animaux-<?= $personnel['id_personnel'] ?>">
                                                                        Voir les animaux
                                                                    </button>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                        <!-- Afficher les animaux sous chaque soigneur -->
                                                        <?php if ((($role_utilisateur === 'cadre' || $role_utilisateur === 'administratif') && $personnel['poste'] === 'soigneur' && !empty($personnel['animaux'])) || ($role_utilisateur === 'soigneur' && !empty($personnel['animaux']))) : ?>
                                                            <tr class="collapse" id="animaux-<?= $personnel['id_personnel'] ?>">
                                                                <td colspan="4">
                                                                    <div class="animal-details">
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
                                                                                <?php foreach ($personnel['animaux'] as $animal) : ?>
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
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>