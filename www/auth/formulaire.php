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
    <style>
        .input-indent::placeholder {
            padding-left: 10px;
            /* Espacement uniquement pour le placeholder */
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <form class="d-flex flex-column flex-md-row flex-wrap justify-content-md-center gap-2" action="/auth/login.php" method="POST">
        <input placeholder="Login" class="rounded input-indent" type="text" id="login" name="login" required>
        <div style="position: relative; display: inline-block;">
            <input placeholder="Password" class="rounded p-2 password-input" type="password" id="mot_de_passe" name="mot_de_passe" required>
            <i class="bi bi-eye" onclick="togglePasswordVisibility()" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; pointer-events: all;"></i>
        </div>
        <button type="submit" class="btn btn-success btn-rounded">Se connecter</button>
    </form>

    <script>
        function togglePasswordVisibility() {
            var passwordInput = document.getElementById('mot_de_passe');
            var eyeIcon = document.querySelector('#mot_de_passe + i');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('bi-eye');
                eyeIcon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('bi-eye-slash');
                eyeIcon.classList.add('bi-eye');
            }
        }
    </script>
<?php
}
?>