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
    $id_espece = (int)$_POST['id_espece'];
    $nom = trim($_POST['nom']);

    // Validation des données
    if (empty($nom)) {
        $_SESSION['error'] = "Le nom de l'espèce est obligatoire.";
        header('Location: gestionEspeces.php');
        exit;
    }

    try {
        // Prépare la requête de mise à jour
        $stmt = $pdo->prepare("UPDATE espece SET nom = :nom WHERE id_espece = :id_espece");
        $stmt->execute([':nom' => $nom, ':id_espece' => $id_espece]);

        // Message de succès avec le nom de l'espèce en gras
        $_SESSION['success'] = "L'espèce <strong>" . htmlspecialchars($nom) . "</strong> a été modifiée avec succès.";
    } catch (PDOException $e) {
        // Gestion des erreurs
        $_SESSION['error'] = "Erreur lors de la modification de l'espèce : " . $e->getMessage();
    }

    // Redirection vers la page de gestion des espèces
    header('Location: gestionEspeces.php');
    exit;
} else {
    // Si le formulaire n'a pas été soumis, redirige vers la page de gestion
    header('Location: gestionEspeces.php');
    exit;
}
