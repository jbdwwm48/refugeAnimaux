<?php
session_start();
require './auth/initDb.php';

// Vérifier si un paramètre de filtre est passé en sécurisant l'entrée
$id_espece = filter_input(INPUT_GET, 'id_espece', FILTER_VALIDATE_INT);

// Définition des couleurs par espèce
$couleurs_especes = [
    'Chien' => '#335c67',
    'Chat' => '#FF9800',
    'Cheval' => '#A0522D',
    'Girafe' => '#FFD54F',
    'Éléphant' => '#9E9E9E',
    'Serpent' => '#2E7D32',
    'Crocodile' => '#9C27B0',
    'Loup' => '#344f64',
    'Âne' => '#5d5737'
];

// Fonction pour obtenir la couleur selon l'espèce (avec une couleur par défaut)
function getCouleurEspece($nom_espece, $couleurs_especes)
{
    return $couleurs_especes[$nom_espece] ?? '#000'; // Noir par défaut si l'espèce n'est pas définie
}

// Requête pour récupérer les animaux, avec ou sans filtre selon la sélection de l'espèce
$sql = "SELECT a.id_animal, a.nom, a.genre, a.historique, 
                COALESCE(a.image, 'https://via.placeholder.com/150') AS image, 
                a.date_naissance, e.nom AS espece 
        FROM animal a
        LEFT JOIN animal_espece ae ON a.id_animal = ae.id_animal
        LEFT JOIN espece e ON ae.id_espece = e.id_espece";

if ($id_espece) {
    $sql .= " WHERE e.id_espece = :id_espece";
}
$sql .= " LIMIT 50"; // Limite pour éviter surcharge

$requete_animaux = $pdo->prepare($sql);

// Si un filtre est appliqué, lier le paramètre pour la requête
if ($id_espece) {
    $requete_animaux->bindParam(':id_espece', $id_espece, PDO::PARAM_INT);
}

$requete_animaux->execute();
$animaux = $requete_animaux->fetchAll(PDO::FETCH_ASSOC);

// Requête pour récupérer les espèces disponibles
$requete_especes = $pdo->query("SELECT id_espece, nom FROM espece");
$especes = $requete_especes->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Refuge des Compagnons Palet</title>
    <link href="./assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="./assets/css/bootstrap-icons.css" rel="stylesheet">
    <style>
        .navbar-custom {
            background-color: rgb(110, 183, 154);
        }

        .footer-custom {
            background-color: rgb(110, 183, 154);
            color: white;
        }

        .card {
            margin: 10px;
        }

        .species-dropdown {
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <?php include('./nav.php'); ?>

    <div class="container mt-4">
        <h1>Bienvenue au Refuge des Compagnons Palet</h1>
        <p class="p-4">Notre refuge accueille des animaux en quête d'un nouveau foyer...</p>
    </div>

    <div class="row w-50 m-auto my-4">
        <div class="col">
            <form method="GET" action="" class="d-flex gap-2 align-items-center">
                <select class="form-select" name="id_espece" aria-label="Filtrer par espèce">
                    <option selected value="">Choisissez une espèce</option>
                    <?php foreach ($especes as $espece) : ?>
                        <option value="<?= $espece['id_espece'] ?>" <?= ($id_espece == $espece['id_espece']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($espece['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary" aria-label="Appliquer le filtre">Filtrer</button>
            </form>
        </div>
    </div>

    <main>
        <section class="d-flex justify-content-center flex-wrap m-auto gap-5">
            <?php if (empty($animaux)) : ?>
                <p class="text-center mt-4">Aucun animal trouvé pour cette espèce.</p>
            <?php else : ?>
                <?php foreach ($animaux as $animal) : ?>
                    <?php $couleur = getCouleurEspece($animal['espece'], $couleurs_especes); ?>
                    <div class="card shadow border-2" style="width: 18rem; border-color: <?= $couleur ?>;">
                        <img src="<?= htmlspecialchars($animal['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($animal['nom']) ?>" loading="lazy">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($animal['nom']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($animal['historique']) ?></p>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item" style="background-color: <?= $couleur ?>; color: white; font-weight: bold;">
                                    Espèce : <?= htmlspecialchars($animal['espece'] ?? 'Non assignée') ?>
                                </li>
                                <li class="list-group-item">Genre : <?= htmlspecialchars($animal['genre']) ?></li>
                                <li class="list-group-item">Date de naissance :
                                    <?= isset($animal['date_naissance']) ? htmlspecialchars((new DateTime($animal['date_naissance']))->format('d/m/Y')) : 'Non renseignée' ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </main>
    <?php include_once('./footer.php'); ?>
    <script src="./assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>