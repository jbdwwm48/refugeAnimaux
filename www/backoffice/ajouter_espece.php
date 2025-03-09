<?php
session_start();
require '../auth/initDb.php';

// Vérifie si l'utilisateur est connecté et a les droits nécessaires
if (!isset($_SESSION['utilisateur'])) {
    header('Location: ../index.php');
    exit;
}

// Vérifie si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupère et nettoie les données du formulaire
    $nom = trim($_POST['nom']);

    // Validation des données
    if (empty($nom)) {
        $_SESSION['error'] = "Le nom de l'espèce est obligatoire.";
        header('Location: gestionEspeces.php');
        exit;
    }

    try {
        // Prépare la requête d'insertion
        $stmt = $pdo->prepare("INSERT INTO espece (nom) VALUES (:nom)");
        $stmt->execute([':nom' => $nom]);

        // Message de succès avec le nom de l'espèce en gras
        $_SESSION['success'] = "L'espèce <strong>" . htmlspecialchars($nom) . "</strong> a été ajoutée avec succès.";
    } catch (PDOException $e) {
        // Gestion des erreurs
        $_SESSION['error'] = "Erreur lors de l'ajout de l'espèce : " . $e->getMessage();
    }

    // Redirection vers la page de gestion des espèces
    header('Location: gestionEspeces.php');
    exit;
} else {
    // Si le formulaire n'a pas été soumis, redirige vers la page de gestion
    header('Location: gestionEspeces.php');
    exit;
}
