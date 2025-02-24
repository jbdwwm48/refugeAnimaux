<?php
session_start(); 
require './auth/initDb.php';

// Vérifier si un paramètre de filtre est passé
$id_espece = isset($_GET['id_espece']) && !empty($_GET['id_espece']) ? (int)$_GET['id_espece'] : null;

// Requête pour récupérer les animaux, avec ou sans filtre selon la sélection de l'espèce
$sql = "SELECT a.id_animal, a.nom, a.genre, a.historique, a.image, a.date_naissance, e.nom AS espece 
        FROM animal a
        INNER JOIN animal_espece ae ON a.id_animal = ae.id_animal
        INNER JOIN espece e ON ae.id_espece = e.id_espece";

if ($id_espece) {
    $sql .= " WHERE e.id_espece = :id_espece"; // Ajouter un filtre par espèce
}

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
    <!-- Bootstrap CSS -->
    <link href="./assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CSS -->
    <link href="./assets/css/bootstrap-icons.css" rel="stylesheet">
    <style>
        .navbar-custom {
            background-color:rgb(110, 183, 154);
            /* Vert */
        }

        .footer-custom {
            background-color:rgb(110, 183, 154);
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
    <!-- Header avec Nav -->
    <?php
    include('./nav.php')
    ?>
    <!-- Introduction -->
    <div class="container mt-4">
        <div class="row">
            <div class="col">
                <h1>Bienvenue au Refuge des Compagnons Palet</h1>
                <p class="p-4">Niché dans le paisible Bourg Palette, notre refuge pour animaux est un havre de paix et de bonheur pour nos amis à quatre pattes. Nous accueillons des animaux de toutes tailles et origines, en leur offrant un environnement sécurisé et affectueux. Refuge des Compagnons Palet est dédié à créer un foyer temporaire chaleureux, où chaque animal peut se détendre, jouer et trouver une famille aimante. Avec des espaces verts luxuriants et des activités stimulantes, notre refuge est l'endroit idéal pour un nouveau départ plein d'amour et de joie pour nos compagnons. 🐾</p>
            </div>
        </div>
    </div>

    <!-- Formulaire pour filtrer par espèce -->
    <div class="row w-50 m-auto">
        <div class="col">
            <form method="GET" action="">
                <select class="form-select" name="id_espece" aria-label="Filtrer par espèce">
                    <option selected value="">Choisissez une espèce</option>
                    <?php foreach ($especes as $espece) : ?>
                        <option value="<?= $espece['id_espece'] ?>" <?= isset($_GET['id_espece']) && $_GET['id_espece'] == $espece['id_espece'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($espece['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-secondary mt-2">Filtrer</button>
            </form>
        </div>
    </div>


    <main>
        <<sectio class="d-flex justify-content-center flex-wrap m-auto gap-5">
            <?php foreach ($animaux as $animal) : ?>
                <div class="card shadow border-2 border-success" style="width: 18rem;">
                    <img src="<?= htmlspecialchars($animal['image'] ?? 'https://via.placeholder.com/150') ?>" class="card-img-top" alt="<?= htmlspecialchars($animal['nom']) ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($animal['nom']) ?></h5>
                        <p class="card-text"><?= htmlspecialchars($animal['historique']) ?></p>
                        <ul class="list-group list-group-flush">
                        <li class="list-group-item">Espèce : <?= htmlspecialchars($animal['espece'] ?? 'Non assignée') ?></li>
                            <li class="list-group-item">Genre : <?= htmlspecialchars($animal['genre']) ?></li>
                            <li class="list-group-item">Date de naissance :
                                <?= isset($animal['date_naissance']) ? htmlspecialchars((new DateTime($animal['date_naissance']))->format('d/m/Y')) : 'Non renseignée' ?>
                            </li>
                        </ul>
                    </div>
                </div>
            <?php endforeach; ?>
            </section>
    </main>
    <!-- Footer -->
    <?php
    include_once('./footer.php')
    ?>
    <script src="./assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>