<?php
// Démarrer la session si elle n'est pas déjà active
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est connecté
if (isset($_SESSION['utilisateur'])) {
    // Vérifier si l'heure de la dernière activité est définie
    if (isset($_SESSION['last_activity'])) {
        // Calculer le temps écoulé depuis la dernière activité
        $inactive_time = time() - $_SESSION['last_activity'];

        // Déconnecter l'utilisateur si inactif pendant plus de 15 minutes (900 secondes)
        if ($inactive_time > 60) {
            // Nettoyer la session
            session_unset();
            session_destroy();

            // Rediriger vers la page de déconnexion
            header('Location: logoutRedirection.php');
            exit;
        }
    }

    // Mettre à jour l'heure de la dernière activité
    $_SESSION['last_activity'] = time();
}
