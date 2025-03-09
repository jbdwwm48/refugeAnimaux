<?php
// Pas besoin de vérifier la session ici, c'est fait dans checkSession.php
?>

<div>
    <?php if (isset($_SESSION['utilisateur']) && is_array($_SESSION['utilisateur'])) : ?>
        <p class='text-white fs-5'>Bienvenue <strong><?= htmlspecialchars($_SESSION['utilisateur']['prenom']) . " " . htmlspecialchars($_SESSION['utilisateur']['nom']) ?> !</strong></p>
        <div class="d-flex gap-2">
            <!-- Bouton Dashboard -->
            <a class="btn btn-primary btn-rounded text-white d-flex align-items-center gap-2" href="/backoffice/dashboard.php">
                <span class="icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-hdd-stack default-icon" viewBox="0 0 16 16">
                        <path d="M14 10a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1v-1a1 1 0 0 1 1-1zM2 9a2 2 0 0 0-2 2v1a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-1a2 2 0 0 0-2-2z" />
                        <path d="M5 11.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0m-2 0a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0M14 3a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1zM2 2a2 2 0 0 0-2 2v1a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2z" />
                        <path d="M5 4.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0m-2 0a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0" />
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-hdd-stack-fill hover-icon" viewBox="0 0 16 16">
                        <path d="M2 9a2 2 0 0 0-2 2v1a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-1a2 2 0 0 0-2-2zm.5 3a.5.5 0 1 1 0-1 .5.5 0 0 1 0 1m2 0a.5.5 0 1 1 0-1 .5.5 0 0 1 0 1M2 2a2 2 0 0 0-2 2v1a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zm.5 3a.5.5 0 1 1 0-1 .5.5 0 0 1 0 1m2 0a.5.5 0 1 1 0-1 .5.5 0 0 1 0 1" />
                    </svg>
                </span>
                <span class="text">Dashboard</span>
            </a>
            <!-- Bouton Déconnexion -->
            <a class="btn btn-danger btn-rounded text-white d-flex align-items-center gap-2" href="/auth/logout.php">
                <span class="icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-x-square default-icon" viewBox="0 0 16 16">
                        <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z" />
                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-x-square-fill hover-icon" viewBox="0 0 16 16">
                        <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm3.354 4.646L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 1 1 .708-.708" />
                    </svg>
                </span>
                <span class="text">Déconnexion</span>
            </a>
        </div>
    <?php else : ?>
        <?php if (isset($_SESSION['error_message'])) : ?>
            <p style='color: red;'><?= htmlspecialchars($_SESSION['error_message']) ?></p>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>
        <!-- Formulaire de connexion -->
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
    <?php endif; ?>
</div>