<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid">
        <a class="navbar-brand text-white" href="#">Refuge des Compagnons Palet</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <div>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="#">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="#">A propos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="#">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="#">Gestion</a>
                    </li>
                </ul>
            </div>
            <div class="d-flex justify-content-md-center ps-md-5">
                <!-- mettre margin  -->
                <form class="d-flex flex-xs-wrap gap-2" action=".php" method="POST">
                    <div>
                        <input placeholder="Utilisateur" type="text" name="utilisateur" id="utilisateur">
                    </div>
                    <div>
                        <input placeholder="Password" type="password" id="password" name="password" required>
                    </div>
                    <div>
                        <button type="submit">Connexion</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php
    // Si un utilisateur est connecté
    if (isset($_SESSION['utilisateur'])) {
        echo "<p>Bienvenue " . htmlspecialchars($_SESSION['utilisateur']) . " !</p>";
        echo '<a href=".php"><button>Déconnexion</button></a>';
    }
    ?>
</nav>