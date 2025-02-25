<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['utilisateur']) && is_array($_SESSION['utilisateur'])) {
    // Afficher un message de bienvenue avec le prénom et le nom
    echo "<p class='text-white fs-5'>Bienvenue " . htmlspecialchars($_SESSION['utilisateur']['prenom']) . " " . htmlspecialchars($_SESSION['utilisateur']['nom']) . " !</p>";
    echo '<div> 
            <a href="/backoffice/dashboard.php"><button class="btn btn-primary btn-rounded text-white">Dashboard</button></a> 
            <a href="/auth/logout.php"><button class="btn btn-danger btn-rounded">Déconnexion</button></a>
          </div>';
} else {
    // Afficher le formulaire de connexion
    if (isset($_SESSION['error_message'])) {
        echo "<p style='color: red;'>" . $_SESSION['error_message'] . "</p>";
        unset($_SESSION['error_message']); // Supprimer le message d'erreur après l'affichage
    }
?>
    <form class="d-flex flex-column flex-md-row flex-wrap justify-content-md-center gap-2" action="/auth/login.php" method="POST">
        <input placeholder="Login" class="rounded" type="text" id="login" name="login" required>
        <input placeholder="Password" class="rounded" type="password" id="mot_de_passe" name="mot_de_passe" required>
        <button type="submit" class="btn btn-success btn-rounded">Se connecter</button>
    </form>
<?php
}
?>