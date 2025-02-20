<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['utilisateur'])) {
    echo "<p>Bienvenue " . htmlspecialchars($_SESSION['utilisateur']) . " !</p>";
    echo '<a href="auth/logout.php"><button>Déconnexion</button></a>';
} else {
    if (isset($_SESSION['error_message'])) {
        echo "<p style='color: red;'>" . $_SESSION['error_message'] . "</p>";
        unset($_SESSION['error_message']); // Supprimer le message d'erreur après l'affichage
    }
?>
    <form class="d-flex flex-column flex-md-row flex-wrap justify-content-md-center gap-2 ps-md-5" action="auth/login.php" method="POST">
        <input type="text" id="login" name="login" required>
        <input type="password" id="mot_de_passe" name="mot_de_passe" required>
        <button type="submit">Se connecter</button>
    </form>
<?php
}
?>