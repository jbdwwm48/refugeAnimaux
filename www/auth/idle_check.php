<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

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
    header('Location: auth/alert.php');
    exit; // Assurer que le script s'arrête après la redirection.
}

// Mettre à jour l'heure de la dernière activité à l'heure actuelle.
// Chaque fois que la page est chargée, cela met à jour la variable de session 'derniere_activite'.
$_SESSION['derniere_activite'] = time();
