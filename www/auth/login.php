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
            header('Location: ../index.php'); // Rediriger vers la page d'accueil
            exit;
        } else {
            $error_message = 'Nom d\'utilisateur ou mot de passe incorrect.';
            // Vous pouvez définir une variable de session pour afficher ce message d'erreur dans le formulaire
            $_SESSION['error_message'] = $error_message;
            header('Location: formulaire.php');
            exit;
        }
    } catch (PDOException $e) {
        $error_message = "Erreur de connexion : " . $e->getMessage();
        $_SESSION['error_message'] = $error_message;
        header('Location: formulaire.php');
        exit;
    }
}
