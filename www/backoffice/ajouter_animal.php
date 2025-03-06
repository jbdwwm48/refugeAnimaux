<?php
session_start();
require '../auth/initDb.php';

// Vérifie si l'utilisateur est connecté et a les droits nécessaires
if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['poste'] === 'soigneur') {
    header('Location: ../index.php');
    exit;
}

// Récupérer toutes les espèces
$sql_all_especes = "SELECT * FROM espece";
$stmt_all_especes = $pdo->prepare($sql_all_especes);
$stmt_all_especes->execute();
$especes = $stmt_all_especes->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    function sanitizeString($str)
    {
        return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
    }

    function validateDate($date)
    {
        return (bool) DateTime::createFromFormat('Y-m-d', $date);
    }

    function validateInt($value)
    {
        return filter_var($value, FILTER_VALIDATE_INT);
    }

    // Nettoyage et validation des données
    $nom = sanitizeString($_POST['nom']);
    $genre = in_array($_POST['genre'], ['M', 'F']) ? $_POST['genre'] : null;
    $numero = sanitizeString($_POST['numero']); // Autoriser les lettres et chiffres
    $pays = sanitizeString($_POST['pays']);
    $date_naissance = validateDate($_POST['date_naissance']) ? $_POST['date_naissance'] : null;
    $date_arrivee = validateDate($_POST['date_arrivee']) ? $_POST['date_arrivee'] : null;
    $historique = sanitizeString($_POST['historique']);
    $id_cage = isset($_POST['cage']) && validateInt($_POST['cage']) ? $_POST['cage'] : null;
    $id_espece = isset($_POST['espece']) && validateInt($_POST['espece']) ? $_POST['espece'] : null;

    // Gestion de l'image : URL ou upload
    $imagePath = null;

    // Vérification de l'URL fournie
    if (!empty($_POST['image_url'])) {
        $image_url = sanitizeString($_POST['image_url']);
        if (filter_var($image_url, FILTER_VALIDATE_URL)) {
            $imagePath = $image_url;
        } else {
            $_SESSION['error'] = "L'URL de l'image est invalide.";
            header('Location: gestionAnimaux.php');
            exit;
        }
    }

    // Vérifier que toutes les données obligatoires sont valides
    if ($nom && $genre && $numero && $pays && $date_naissance && $date_arrivee && $id_cage && $id_espece) {
        try {
            // Insérer l'animal dans la table `animal`
            $stmt = $pdo->prepare("
                INSERT INTO animal (nom, genre, numero, pays, date_naissance, date_arrivee, historique, image, id_cage)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$nom, $genre, $numero, $pays, $date_naissance, $date_arrivee, $historique, $imagePath, $id_cage]);

            // Récupérer l'ID de l'animal inséré
            $id_animal = $pdo->lastInsertId();

            // Lier l'animal à l'espèce dans la table `animal_espece`
            $stmt = $pdo->prepare("
                INSERT INTO animal_espece (id_animal, id_espece)
                VALUES (?, ?)
            ");
            $stmt->execute([$id_animal, $id_espece]);

            $_SESSION['success'] = "L'animal a été ajouté avec succès.";
        } catch (Exception $e) {
            $_SESSION['error'] = "Erreur lors de l'ajout de l'animal : " . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = "Données invalides ou manquantes.";
    }

    // Redirection
    header('Location: gestionAnimaux.php');
    exit;
}