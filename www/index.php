<?php
session_start();
require './auth/initDb.php';

// Vérifier si un paramètre de filtre est passé en sécurisant l'entrée
$id_espece = filter_input(INPUT_GET, 'id_espece', FILTER_VALIDATE_INT);

// Définition des couleurs par espèce avec bordure et fond du li
$couleurs_especes = [
    'Chien' => ['border' => '#1A3C34', 'li_bg' => '#A8D5BA', 'li_text' => '#1A3C34'], // Vert profond -> Vert clair
    'Chat' => ['border' => '#D84315', 'li_bg' => '#FFCCBC', 'li_text' => '#D84315'], // Orange vif -> Orange pâle
    'Cheval' => ['border' => '#4A2C2A', 'li_bg' => '#D4A5A5', 'li_text' => '#4A2C2A'], // Marron riche -> Marron clair
    'Girafe' => ['border' => '#F9A825', 'li_bg' => '#FFF9C4', 'li_text' => '#F9A825'], // Jaune doré -> Jaune pâle
    'Éléphant' => ['border' => '#37474F', 'li_bg' => '#CFD8DC', 'li_text' => '#37474F'], // Gris foncé -> Gris clair
    'Serpent' => ['border' => '#2E7D32', 'li_bg' => '#C8E6C9', 'li_text' => '#2E7D32'], // Vert émeraude -> Vert pâle
    'Crocodile' => ['border' => '#4A148C', 'li_bg' => '#D1C4E9', 'li_text' => '#4A148C'], // Violet profond -> Violet clair
    'Loup' => ['border' => '#0D1B2A', 'li_bg' => '#B0BEC5', 'li_text' => '#0D1B2A'], // Bleu-gris foncé -> Bleu-gris clair
    'Âne' => ['border' => '#78909C', 'li_bg' => '#ECEFF1', 'li_text' => '#455A64'] // Gris bleuté -> Gris très clair
];
$couleur_defaut = ['border' => '#455A64', 'li_bg' => '#CFD8DC', 'li_text' => '#455A64']; // Gris bleuté par défaut

// Fonction pour obtenir les couleurs selon l'espèce
function getCouleursEspece($nom_espece, $couleurs_especes, $couleur_defaut)
{
    return $couleurs_especes[$nom_espece] ?? $couleur_defaut;
}

// Requête pour récupérer les animaux
$sql = "SELECT a.id_animal, a.nom, a.genre, a.historique, 
                COALESCE(a.image, 'https://via.placeholder.com/150') AS image, 
                a.date_naissance, GROUP_CONCAT(e.nom ORDER BY e.nom ASC) AS espece 
        FROM animal a
        LEFT JOIN animal_espece ae ON a.id_animal = ae.id_animal
        LEFT JOIN espece e ON ae.id_espece = e.id_espece";

if ($id_espece) {
    $sql .= " WHERE e.id_espece = :id_espece";
}

$sql .= " GROUP BY a.id_animal LIMIT 50";

$requete_animaux = $pdo->prepare($sql);
if ($id_espece) {
    $requete_animaux->bindParam(':id_espece', $id_espece, PDO::PARAM_INT);
}
$requete_animaux->execute();
$animaux = $requete_animaux->fetchAll(PDO::FETCH_ASSOC);

// Requête pour les espèces disponibles
$requete_especes = $pdo->query("SELECT id_espece, nom FROM espece");
$especes = $requete_especes->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PURRfect Home ~ Refuge pour Animaux</title>
    <link href="./assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="./assets/css/bootstrap-icons.css" rel="stylesheet">
    <style>
        .navbar-custom,
        .footer-custom {
            background-color: #58d9ce;
            color: white;
        }

        .card {
            margin: 10px;
            border-width: 3px;
            width: 18rem;
        }

        .espece-li {
            font-weight: bold;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
        }

        .species-dropdown {
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <?php include('./nav.php'); ?>

    <div class="container mt-4">
        <h1>Bienvenue au Refuge <strong>PURR</strong>fect Home ~</h1>
        <p class="p-4">Du ronron des chats au barrissement des éléphants, chaque animal trouve sa place. Notre refuge offre un havre de paix aux compagnons petits et grands, sauvages ou doux !</p>
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
                    <?php $couleurs = getCouleursEspece($animal['espece'], $couleurs_especes, $couleur_defaut); ?>
                    <div class="card shadow border-2" style="border-color: <?= $couleurs['border'] ?>;">
                        <img src="<?= htmlspecialchars($animal['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($animal['nom']) ?>" loading="lazy">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($animal['nom']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($animal['historique']) ?></p>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item espece-li" style="background-color: <?= $couleurs['li_bg'] ?>; color: <?= $couleurs['li_text'] ?>;">
                                    Espèce: <?= htmlspecialchars($animal['espece'] ?? 'Non assignée') ?>
                                </li>
                                <li class="list-group-item">Genre: <?= htmlspecialchars($animal['genre']) ?></li>
                                <li class="list-group-item">Date de naissance:
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