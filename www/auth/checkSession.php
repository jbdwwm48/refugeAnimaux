<?php
// Démarrer la session si elle n'est pas déjà active
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Définir une liste de pages accessibles sans être connecté
$publicPages = ['/index.php', '/auth/login.php', '/auth/logout.php', '/auth/logoutRedirection.php'];

// Obtenir l'URI actuelle (pour vérifier si la page est publique)
$currentUri = $_SERVER['REQUEST_URI'];

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur']) || !is_array($_SESSION['utilisateur'])) {
    // Si la page actuelle n'est pas dans la liste des pages publiques, rediriger vers une page de connexion
    if (!in_array($currentUri, $publicPages)) {
        header('Location: /index.php'); // Rediriger vers index.php ou une page de connexion
        exit;
    }
} else {
    // Si l'utilisateur est connecté, récupérer ses informations
    $id_utilisateur = $_SESSION['utilisateur']['id_personnel'];
    $role_utilisateur = $_SESSION['utilisateur']['poste'];
    $nom_utilisateur = $_SESSION['utilisateur']['nom'];
    $prenom_utilisateur = $_SESSION['utilisateur']['prenom'];

    // Vérifier l'inactivité
    if (isset($_SESSION['last_activity'])) {
        // Calculer le temps écoulé depuis la dernière activité
        $inactive_time = time() - $_SESSION['last_activity'];

        // Déconnecter l'utilisateur si inactif pendant plus de 30 secondes (pour les tests)
        if ($inactive_time > 30) {
            // Nettoyer la session
            session_unset();
            session_destroy();

            // Rediriger vers la page de déconnexion
            header('Location: /auth/logoutRedirection.php');
            exit;
        }
    }

    // Mettre à jour l'heure de la dernière activité
    $_SESSION['last_activity'] = time();
}
