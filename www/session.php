<form action=".php" method="POST">
    <label for="utilisateur">Utilisateur</label>
    <input type="text" name="utilisateur" id="utilisateur">
    <label for="password">Mot de passe :</label>
    <input type="password" id="password" name="password" required>
    <button type="submit">Connexion</button>
</form>
<?php
// Si un utilisateur est connecté
if (isset($_SESSION['prenom'])) {
    echo "<p>Bienvenue " . htmlspecialchars($_SESSION['prenom']) . " !</p>";
    echo '<a href="logout.php"><button>Déconnexion</button></a>';
}
?>