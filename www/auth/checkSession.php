<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur'])) {
    header('Location: ../auth/formulaire.php');
    exit;
}

// Récupérer les informations de l'utilisateur
$id_utilisateur = $_SESSION['utilisateur']['id_personnel'];
$role_utilisateur = $_SESSION['utilisateur']['role'];
$nom_utilisateur = $_SESSION['utilisateur']['nom'];
$prenom_utilisateur = $_SESSION['utilisateur']['prenom'];
