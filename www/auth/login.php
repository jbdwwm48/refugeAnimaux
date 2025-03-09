<?php
// Démarrer la session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Inclure le fichier d'initialisation de la base de données
require 'initDb.php';

// Vérifier si la requête est de type POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $login = $_POST['login'];
    $mot_de_passe = $_POST['mot_de_passe'];

    // Vérifier les identifiants dans la base de données
    $requete = $pdo->prepare("SELECT id_personnel, nom, prenom, poste, login, mot_de_passe FROM personnel WHERE login = ?");
    $requete->execute([$login]);
    $utilisateur = $requete->fetch(PDO::FETCH_ASSOC);

    // Si l'utilisateur existe et que le mot de passe est correct
    if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
        // Authentification réussie : initialiser la session
        $_SESSION['utilisateur'] = [
            'id_personnel' => $utilisateur['id_personnel'], // ID de l'utilisateur
            'nom' => $utilisateur['nom'], // Nom de l'utilisateur
            'prenom' => $utilisateur['prenom'], // Prénom de l'utilisateur
            'poste' => strtolower($utilisateur['poste']), // Poste de l'utilisateur (converti en minuscules)
            'login' => $utilisateur['login'] // Login de l'utilisateur
        ];

        // Définir l'heure de la dernière activité
        $_SESSION['last_activity'] = time();

        // Rediriger vers le tableau de bord
        header('Location: ../backoffice/dashboard.php');
        exit;
    } else {
        // Authentification échouée : définir un message d'erreur
        $_SESSION['error_message'] = "Identifiant ou mot de passe incorrect.";

        // Rediriger vers le formulaire de connexion
        header('Location: formulaire.php');
        exit;
    }
}
