<?php
session_start();
include 'initDb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'];
    $mot_de_passe = $_POST['mot_de_passe'];

    try {
        $stmt = $pdo->prepare('SELECT * FROM personnel WHERE login = ?');
        $stmt->execute([$login]);
        $user = $stmt->fetch();

        if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
            $_SESSION['utilisateur'] = $user['login'];
            $_SESSION['derniere_activite'] = time(); // Stocker l'heure de la dernière activité
            unset($_SESSION['error_message']); // Supprimer le message d'erreur en cas de connexion réussie
            header('Location: ../index.php'); // Rediriger vers la page d'accueil
            exit;
        } else {
            $_SESSION['error_message'] = 'Nom d\'utilisateur ou mot de passe incorrect.';
            header('Location: ../index.php'); // Rester sur la page actuelle
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Erreur de connexion : " . $e->getMessage();
        header('Location: ../index.php'); // Rester sur la page actuelle
        exit;
    }
}
