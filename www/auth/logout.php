<?php
session_start();

// Définir la durée maximale d'inactivité en secondes. Ici, 15 minutes (15 * 60 secondes).
$inactivite_max = 1 * 60;

// Vérifier si la variable 'derniere_activite' existe dans la session et si l'utilisateur a été inactif pendant plus de 15 minutes.
if (isset($_SESSION['derniere_activite']) && (time() - $_SESSION['derniere_activite']) > $inactivite_max) {
    // Si l'utilisateur a été inactif pendant plus de 15 minutes, détruire la session pour déconnecter l'utilisateur.

    // Supprimer toutes les variables de session.
    session_unset();

    // Détruire la session.
    session_destroy();

    // Rediriger l'utilisateur vers une page d'alerte.
    header('Location: alert.php');
    exit; // Assurer que le script s'arrête après la redirection.
}

// Si l'utilisateur est déjà connecté, afficher un message de déconnexion.
if (isset($_SESSION['utilisateur'])) {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}

header('Location: login.php');
exit;
