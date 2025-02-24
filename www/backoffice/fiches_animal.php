<?php
session_start();
require '../auth/initDb.php'; 

// Vérifier si un ID est fourni
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID de l'animal manquant.");
}

$id_animal = (int)$_GET['id'];

// Récupérer les informations de l'animal
$requete = $pdo->prepare("SELECT a.*, c.numero AS cage FROM animal a LEFT JOIN cage c ON a.id_cage = c.id_cage WHERE a.id_animal = ?");
$requete->execute([$id_animal]);
$animal = $requete->fetch(PDO::FETCH_ASSOC);

if (!$animal) {
    die("Animal introuvable.");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche de l'animal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4 text-center">Fiche de <?= htmlspecialchars($animal['nom']) ?></h1>
        <div class="card mx-auto" style="max-width: 500px;">
            <img src="<?= !empty($animal['photo']) ? htmlspecialchars($animal['photo']) : 'default.jpg' ?>" class="card-img-top" alt="Photo de l'animal">
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($animal['nom']) ?></h5>
                <p class="card-text"><strong>Genre :</strong> <?= htmlspecialchars($animal['genre']) ?></p>
                <p class="card-text"><strong>Numéro :</strong> <?= htmlspecialchars($animal['numero']) ?></p>
                <p class="card-text"><strong>Cage :</strong> <?= $animal['cage'] ?? 'Non assignée' ?></p>
                <p class="card-text"><strong>Description :</strong> <?= nl2br(htmlspecialchars($animal['description'] ?? 'Aucune description disponible.')) ?></p>
                <a href="index.php" class="btn btn-primary">Retour</a>
            </div>
        </div>
    </div>
</body>
</html>
