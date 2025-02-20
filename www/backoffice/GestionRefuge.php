<?php
session_start();
require '../auth/initDb.php'; 

$utilisateur_connecte = isset($_SESSION['id_personnel']);

$requete_animaux = $pdo->query("SELECT a.id_animal, a.nom, a.genre, a.numero, c.numero AS cage FROM animal a LEFT JOIN cage c ON a.id_cage = c.id_cage");
$animaux = $requete_animaux->fetchAll(PDO::FETCH_ASSOC);

$requete_cages = $pdo->query("SELECT c.id_cage, c.numero, COUNT(a.id_animal) AS occupation FROM cage c LEFT JOIN animal a ON c.id_cage = a.id_cage GROUP BY c.id_cage");
$cages = $requete_cages->fetchAll(PDO::FETCH_ASSOC);

$personnels = [];
if ($utilisateur_connecte) {
    $requete_personnels = $pdo->query("SELECT id_personnel, nom, prenom, poste FROM personnel");
    $personnels = $requete_personnels->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backoffice Refuge</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .table-responsive { max-width: 800px; margin: auto; }
        .table-sm td, .table-sm th { padding: 0.3rem; font-size: 0.85rem; }
        .progress-bar { font-size: 0.75rem; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4 text-center">Backoffice - Gestion des Animaux</h1>
        
        <h2 class="mt-4 text-center">Liste des Animaux</h2>
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-striped text-center">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Genre</th>
                        <th>Numéro</th>
                        <th>Cage</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($animaux as $animal): ?>
                    <tr>
                        <td><?= $animal['id_animal'] ?></td>
                        <td><?= htmlspecialchars($animal['nom']) ?></td>
                        <td><?= htmlspecialchars($animal['genre']) ?></td>
                        <td><?= htmlspecialchars($animal['numero']) ?></td>
                        <td><?= $animal['cage'] ?? 'Non assignée' ?></td>
                        <td><a href="localhost/backoffice/fiche_animal.php?id=3" class="btn btn-info btn-sm">Voir</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <h2 class="mt-4 text-center">Occupation des Cages</h2>
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-striped text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Numéro</th>
                        <th>Occupation</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cages as $cage): ?>
                    <tr>
                        <td><?= $cage['numero'] ?></td>
                        <td>
                            <div class="progress">
                                <div class="progress-bar bg-success" role="progressbar" style="width: <?= ($cage['occupation'] > 0) ? '100%' : '0%' ?>;" aria-valuenow="<?= $cage['occupation'] ?>" aria-valuemin="0" aria-valuemax="1">
                                    <?= $cage['occupation'] > 0 ? 'Occupée' : 'Libre' ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php if ($utilisateur_connecte): ?>
        <h2 class="mt-4 text-center">Liste des Employés</h2>
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-striped text-center">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Poste</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($personnels as $personnel): ?>
                    <tr>
                        <td><?= $personnel['id_personnel'] ?></td>
                        <td><?= htmlspecialchars($personnel['nom']) ?></td>
                        <td><?= htmlspecialchars($personnel['prenom']) ?></td>
                        <td><?= htmlspecialchars($personnel['poste']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
