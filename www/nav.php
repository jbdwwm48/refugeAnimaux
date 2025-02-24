<nav class="navbar navbar-expand-lg navbar-custom px- ">
    <div class="container-fluid ">
        <img src="https://mdbootstrap.com/img/Photos/new-templates/animal-shelter/logo.png" height="70" alt="logo du refuge" loading="lazy" />
        <button class="navbar-toggler bg-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-between" id="navbarNav">
            <div>
                <ul class="navbar-nav gap-4">
                    <li class="nav-item ">
                        <a class="nav-link text-white fs-5" href="./index.php">Accueil</a>
                    </li>
                    <li class="nav-item ">
                        <a class="nav-link text-white fs-5" href="./footer.php">A propos</a>
                    </li>
                </ul>
            </div>
            <div>
                <?php include_once('auth/formulaire.php'); ?>
            </div>
        </div>
    </div>
</nav>
