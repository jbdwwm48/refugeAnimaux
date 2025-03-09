<?php
session_start();
require '../auth/initDb.php'; // Connexion à la base de données

// Fonction de nettoyage et validation
function sanitizeString($str)
{
    return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
}

function validateInt($value)
{
    return filter_var($value, FILTER_VALIDATE_INT);
}

function validateDate($date)
{
    return (bool) DateTime::createFromFormat('Y-m-d', $date);
}

// Vérifier si un ID d’animal est passé en paramètre
if (!isset($_GET['id']) || !validateInt($_GET['id'])) {
    die('ID d’animal manquant ou invalide.');
}

$id_animal = $_GET['id'];

// Récupérer les infos de l’animal
$sql = "SELECT * FROM animal WHERE id_animal = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_animal]);
$animal = $stmt->fetch();

if (!$animal) {
    die('Animal introuvable.');
}

// Récupérer l'espèce de l'animal
$sql_espece = "SELECT e.id_espece, e.nom FROM espece e
                JOIN animal_espece ae ON ae.id_espece = e.id_espece
                WHERE ae.id_animal = ?";
$stmt_espece = $pdo->prepare($sql_espece);
$stmt_espece->execute([$id_animal]);
$espece_animal = $stmt_espece->fetch();

// Récupérer toutes les espèces
$sql_all_especes = "SELECT * FROM espece";
$stmt_all_especes = $pdo->prepare($sql_all_especes);
$stmt_all_especes->execute();
$especes = $stmt_all_especes->fetchAll();

// Récupérer les cages libres
$sql_cages_libres = "
    SELECT c.* 
    FROM cage c 
    LEFT JOIN animal a ON c.id_cage = a.id_cage 
    WHERE a.id_cage IS NULL OR c.id_cage = ?
";
$stmt_cages_libres = $pdo->prepare($sql_cages_libres);
$stmt_cages_libres->execute([$animal['id_cage']]); // Inclure la cage actuelle de l'animal
$cages_libres = $stmt_cages_libres->fetchAll();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Nettoyage et validation des données
    $nom = sanitizeString($_POST['nom']);
    $genre = in_array($_POST['genre'], ['M', 'F']) ? $_POST['genre'] : null;

    // Récupérer le numéro (peut contenir des lettres et des chiffres)
    $numero = sanitizeString($_POST['numero']); // Utilisez sanitizeString au lieu de validateInt

    $pays = sanitizeString($_POST['pays']);
    $date_naissance = validateDate($_POST['date_naissance']) ? $_POST['date_naissance'] : null;
    $date_arrivee = validateDate($_POST['date_arrivee']) ? $_POST['date_arrivee'] : null;
    $historique = sanitizeString($_POST['historique']);

    // Récupérer et valider l'URL de l'image
    $image = sanitizeString($_POST['image']);
    if (!empty($image) && !filter_var($image, FILTER_VALIDATE_URL)) {
        $_SESSION['error'] = "L'URL de l'image est invalide.";
    }

    // Récupérer la cage sélectionnée ou garder la cage actuelle
    $id_cage = validateInt($_POST['cage']) ? $_POST['cage'] : $animal['id_cage'];

    $id_espece = validateInt($_POST['espece']) ? $_POST['espece'] : null;

    if ($nom && $genre && $numero && $pays && $date_naissance && $date_arrivee && $id_espece) {
        // Mise à jour des informations de l'animal
        $update_sql = "UPDATE animal SET nom = ?, genre = ?, numero = ?, pays = ?, date_naissance = ?, date_arrivee = ?, historique = ?, image = ?, id_cage = ? WHERE id_animal = ?";
        $stmt = $pdo->prepare($update_sql);

        if ($stmt->execute([$nom, $genre, $numero, $pays, $date_naissance, $date_arrivee, $historique, $image, $id_cage, $id_animal])) {
            // Supprimer l'ancienne espèce
            $delete_espece_sql = "DELETE FROM animal_espece WHERE id_animal = ?";
            $pdo->prepare($delete_espece_sql)->execute([$id_animal]);

            // Ajouter la nouvelle espèce
            $update_espece_sql = "INSERT INTO animal_espece (id_animal, id_espece) VALUES (?, ?)";
            if ($pdo->prepare($update_espece_sql)->execute([$id_animal, $id_espece])) {
                $_SESSION['success'] = "Mise à jour réussie !";
                header("Location: GestionAnimaux.php");
                exit;
            }
        }
    }
    $_SESSION['error'] = "Erreur lors de la mise à jour.";
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Animal</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light w-75 m-auto">
    <div class="container mt-5">
        <h2>Modifier un animal</h2>

        <?php
        if (isset($_SESSION['error'])) {
            echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
            unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
            echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
            unset($_SESSION['success']);
        }
        ?>

        <form method="POST">
            <div class="form-group">
                <label for="nom">Nom</label>
                <input type="text" class="form-control" name="nom" id="nom" value="<?= htmlspecialchars($animal['nom']) ?>" required>
            </div>

            <div class="form-group">
                <label for="genre">Genre</label>
                <select class="form-control" name="genre" id="genre" required>
                    <option value="M" <?= $animal['genre'] == 'M' ? 'selected' : '' ?>>Mâle</option>
                    <option value="F" <?= $animal['genre'] == 'F' ? 'selected' : '' ?>>Femelle</option>
                </select>
            </div>

            <div class="form-group">
                <label for="numero">Numéro</label>
                <input type="text" class="form-control" name="numero" id="numero" value="<?= $animal['numero'] ?>">
            </div>

            <div class="form-group">
                <label for="pays">Pays d'origine</label>
                <input type="text" class="form-control" name="pays" id="pays" value="<?= $animal['pays'] ?>" required>
            </div>
            <div class="form-group">
                <label for="date_naissance">Date de naissance</label>
                <input type="date" class="form-control" name="date_naissance" id="date_naissance" value="<?= $animal['date_naissance'] ?>" required>
            </div>

            <div class="form-group">
                <label for="date_arrivee">Date d'arrivée</label>
                <input type="date" class="form-control" name="date_arrivee" id="date_arrivee" value="<?= $animal['date_arrivee'] ?>" required>
            </div>

            <div class="form-group">
                <label for="historique">Historique</label>
                <textarea class="form-control" name="historique" id="historique" required><?= $animal['historique'] ?></textarea>
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">URL de l'image</label>
                <input type="url" class="form-control" id="image" name="image" placeholder="https://exemple.com/image.jpg" value="<?= htmlspecialchars($animal['image']) ?>" oninput="previewImage()">
                <img id="imagePreview" src="<?= htmlspecialchars($animal['image']) ?>" alt="Aperçu" class="mt-2" style="max-width: 200px; display: <?= !empty($animal['image']) ? 'block' : 'none' ?>;">
            </div>

            <div class="form-group">
                <label for="cage">Cage</label>
                <select class="form-control" name="cage" id="cage">
                    <option value="">Sélectionner une cage</option>
                    <?php foreach ($cages_libres as $cage) : ?>
                        <option value="<?= $cage['id_cage'] ?>" <?= ($animal['id_cage'] == $cage['id_cage']) ? 'selected' : '' ?>>
                            Cage #<?= htmlspecialchars($cage['numero']) ?>
                            <?= ($animal['id_cage'] == $cage['id_cage']) ? '(Actuelle)' : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="espece">Espèce</label>
                <select class="form-control" name="espece" id="espece" required>
                    <?php foreach ($especes as $espece) : ?>
                        <option value="<?= $espece['id_espece'] ?>" <?= $espece['id_espece'] == $espece_animal['id_espece'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($espece['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Mettre à jour</button>
            <a href="./GestionAnimaux.php" class="btn btn-danger active" role="button" data-bs-toggle="button" aria-pressed="true">Annuler</a>
        </form>
    </div>
</body>

<script>
    function previewImage() {
        const imageUrl = document.getElementById('image').value;
        const imagePreview = document.getElementById('imagePreview');
        if (imageUrl) {
            imagePreview.src = imageUrl;
            imagePreview.style.display = 'block';
        } else {
            imagePreview.style.display = 'none';
        }
    }
</script>

</html>