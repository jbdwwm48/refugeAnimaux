<?php

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Refuge des Compagnons Palet</title>
    <!-- Bootstrap CSS -->
    <link href="./assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CSS -->
    <link href="./assets/css/bootstrap-icons.css" rel="stylesheet">
    <style>
        .navbar-custom {
            background-color: #4CAF50;
            /* Vert */
        }

        .footer-custom {
            background-color: #4CAF50;
            color: white;
        }

        .card {
            margin: 10px;
        }

        .species-dropdown {
            margin: 20px 0;
        }
    </style>
</head>

<body>

    <!-- Header avec Nav -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand text-white" href="#">Refuge des Compagnons Palet</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
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
        </div>
    </nav>

    <!-- Introduction -->
    <div class="container mt-4">
        <div class="row">
            <div class="col">
                <h1>Bienvenue au Refuge des Compagnons Palet</h1>
                <p>Niché dans le paisible Bourg Palette, notre refuge pour animaux est un havre de paix et de bonheur pour nos amis à quatre pattes. Nous accueillons des animaux de toutes tailles et origines, en leur offrant un environnement sécurisé et affectueux. Refuge des Compagnons Palet est dédié à créer un foyer temporaire chaleureux, où chaque animal peut se détendre, jouer et trouver une famille aimante. Avec des espaces verts luxuriants et des activités stimulantes, notre refuge est l'endroit idéal pour un nouveau départ plein d'amour et de joie pour nos compagnons. 🐾</p>
            </div>
        </div>
    </div>

    <!-- Barre de recherche avec dropdown -->
    <div class="container species-dropdown w-25 ">
        <div class="row">
            <div class="col">
                <select class="form-select" aria-label="Default select example">
                    <option selected>Choisissez une espèce</option>
                    <option value="1">Ane</option>
                    <option value="2">Chat</option>
                    <option value="3">Cheval</option>
                    <option value="4">Chien</option>
                    <option value="5">Crocodile</option>
                    <option value="6">Éléphant</option>
                    <option value="7">Girafe</option>
                    <option value="8">Loup</option>
                    <option value="9">Serpent</option>
                    <option value="10">Autre</option>
                </select>
            </div>
        </div>
    </div>

    <main>
        <section class="d-flex justify-content-center flex-wrap w-75 m-auto gap-5 ">
            <div class="card shadow border-2 border-success" style="width: 18rem ;">
                <img src="https://cdn.pixabay.com/photo/2015/09/30/16/50/pug-965766_1280.jpg" class="card-img-top" alt="chien">
                <div class="card-body">
                    <h5 class="card-title">Nom de l'animal</h5>
                    <p class="card-text">historique ____ Some quick example text to build on the card title and make up the bulk of the card's
                        content.</p>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item bg-success bg-opacity-50">Especes- CHIEN</li>
                        <li class="list-group-item">Male / Female</li>
                        <li class="list-group-item">Date de naissance</li>
                    </ul>
                    <!-- <div class="card-body">
            <a href="#" class="card-link">Card link</a>
            <a href="#" class="card-link">Another link</a>
        </div> -->
                </div>
            </div>

            <div class="card shadow border-2 border-success " style="width: 18rem ;">
                <img src="https://cdn.pixabay.com/photo/2017/12/21/12/12/cat-3031684_640.jpg" class="card-img-top" alt="chat">
                <div class="card-body">
                    <h5 class="card-title">Nom de l'animal</h5>
                    <p class="card-text">historique ____ Some quick example text to build on the card title and make up the bulk of the card's
                        content.</p>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item bg-success  bg-opacity-25">Especes - CHAT</li>
                        <li class="list-group-item">Male / Female</li>
                        <li class="list-group-item">Date de naissance</li>
                    </ul>
                    <!-- <div class="card-body">
            <a href="#" class="card-link">Card link</a>
            <a href="#" class="card-link">Another link</a>
        </div> -->
                </div>
            </div>

            <div class="card shadow border-2 border-dark" style="width: 18rem ;">
                <img src="https://cdn.pixabay.com/photo/2018/11/15/22/20/horse-3818264_640.jpg" class="card-img-top" alt="...">
                <div class="card-body">
                    <h5 class="card-title">Nom de l'animal</h5>
                    <p class="card-text">historique ____ Some quick example text to build on the card title and make up the bulk of the card's
                        content.</p>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item bg-dark bg-opacity-25">Especes - Cheval </li>
                        <li class="list-group-item">Male / Female</li>
                        <li class="list-group-item">Date de naissance</li>
                    </ul>
                    <!-- <div class="card-body">
            <a href="#" class="card-link">Card link</a>
            <a href="#" class="card-link">Another link</a>
        </div> -->
                </div>
            </div>


            <div class="card shadow border-2 border-primary" style="width: 18rem ;">
                <img src="https://cdn.pixabay.com/photo/2019/04/17/18/13/donkey-4134955_640.jpg" class="card-img-top" alt="...">
                <div class="card-body">
                    <h5 class="card-title">Nom de l'animal</h5>
                    <p class="card-text">historique ____ Some quick example text to build on the card title and make up the bulk of the card's
                        content.</p>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item bg-primary bg-opacity-25">Especes - Ane </li>
                        <li class="list-group-item">Male / Female</li>
                        <li class="list-group-item">Date de naissance</li>
                    </ul>
                    <!-- <div class="card-body">
            <a href="#" class="card-link">Card link</a>
            <a href="#" class="card-link">Another link</a>
        </div> -->
                </div>
            </div>

            <div class="card shadow border-2 border-info" style="width: 18rem ;">
                <img src="https://cdn.pixabay.com/photo/2019/04/14/19/09/jordan-4127519_1280.jpg" class="card-img-top" alt="...">
                <div class="card-body">
                    <h5 class="card-title">Mulet</h5>
                    <p class="card-text">historique ____ Some quick example text to build on the card title and make up the bulk of the card's
                        content.</p>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item bg-info bg-opacity-25">Especes - Autre </li>
                        <li class="list-group-item">Male / Female</li>
                        <li class="list-group-item">Date de naissance</li>
                    </ul>
                    <!-- <div class="card-body">
                    <a href="#" class="card-link">Card link</a>
                    <a href="#" class="card-link">Another link</a>
                    </div> -->
                </div>
            </div>

            <div class="card shadow border-2 border-info" style="width: 18rem ;">
                <img src="https://cdn.pixabay.com/photo/2020/11/22/20/39/giraffe-5767909_640.jpg" class="card-img-top" alt="...">
                <div class="card-body">
                    <h5 class="card-title">Nom de l'animal</h5>
                    <p class="card-text">historique ____ Some quick example text to build on the card title and make up the bulk of the card's
                        content.</p>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item bg-info bg-opacity-25">Especes - Girafe </li>
                        <li class="list-group-item">Male / Female</li>
                        <li class="list-group-item">Date de naissance</li>
                    </ul>
                    <!-- <div class="card-body">
            <a href="#" class="card-link">Card link</a>
            <a href="#" class="card-link">Another link</a>
        </div> -->
                </div>
            </div>

            <div class="card shadow border-2 border-primary" style="width: 18rem ;">
                <img src="https://cdn.pixabay.com/photo/2018/10/16/10/41/elephant-3751167_640.jpg" class="card-img-top" alt="...">
                <div class="card-body">
                    <h5 class="card-title">Nom de l'animal</h5>
                    <p class="card-text">historique ____ Some quick example text to build on the card title and make up the bulk of the card's
                        content.</p>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item bg-primary bg-opacity-25">Especes - Elephant </li>
                        <li class="list-group-item">Male / Female</li>
                        <li class="list-group-item">Date de naissance</li>
                    </ul>
                    <!-- <div class="card-body">
            <a href="#" class="card-link">Card link</a>
            <a href="#" class="card-link">Another link</a>
        </div> -->
                </div>
            </div>

            <div class="card shadow border-2 border-warning" style="width: 18rem ;">
                <img src="https://cdn.pixabay.com/photo/2021/01/29/19/28/arctic-wolf-5961985_640.jpg" class="card-img-top" alt="...">
                <div class="card-body">
                    <h5 class="card-title">Nom de l'animal</h5>
                    <p class="card-text">historique ____ Some quick example text to build on the card title and make up the bulk of the card's
                        content.</p>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item bg-warning bg-opacity-25">Especes - Loup</li>
                        <li class="list-group-item">Male / Female</li>
                        <li class="list-group-item">Date de naissance</li>
                    </ul>
                    <!-- <div class="card-body">
            <a href="#" class="card-link">Card link</a>
            <a href="#" class="card-link">Another link</a>
        </div> -->
                </div>
            </div>

            <div class="card shadow border-2 border-danger-emphasis" style="width: 18rem ;">
                <img src="https://cdn.pixabay.com/photo/2021/09/28/00/20/snake-6662549_640.jpg" class="card-img-top" alt="...">
                <div class="card-body">
                    <h5 class="card-title">Nom de l'animal</h5>
                    <p class="card-text">historique ____ Some quick example text to build on the card title and make up the bulk of the card's
                        content.</p>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item bg-danger-emphasis bg-opacity-50">Especes - Serpent</li>
                        <li class="list-group-item">Male / Female</li>
                        <li class="list-group-item">Date de naissance</li>
                    </ul>
                    <!-- <div class="card-body">
            <a href="#" class="card-link">Card link</a>
            <a href="#" class="card-link">Another link</a>
        </div> -->
                </div>
            </div>

            <div class="card shadow border-2 border-danger" style="width: 18rem ;">
                <img src="https://cdn.pixabay.com/photo/2016/02/19/14/17/crocodile-1210331_640.jpg" class="card-img-top" alt="croco">
                <div class="card-body">
                    <h5 class="card-title">Nom de l'animal</h5>
                    <p class="card-text">historique ____ Some quick example text to build on the card title and make up the bulk of the card's
                        content.</p>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item bg-danger bg-opacity-25">Especes - Croco</li>
                        <li class="list-group-item">Male / Female</li>
                        <li class="list-group-item">Date de naissance</li>
                    </ul>
                    <!-- <div class="card-body">
            <a href="#" class="card-link">Card link</a>
            <a href="#" class="card-link">Another link</a>
        </div> -->
                </div>
            </div>

        </section>

    </main>

    <!-- Footer -->
    <footer class="footer-custom text-center text-lg-start mt-4">
        <div class="container p-4">
            <div class="row">
                <div class="col-lg-6 col-md-12 mb-4 mb-md-0">
                    <h5 class="text-uppercase">Liens utiles</h5>
                    <ul class="list-unstyled mb-0">
                        <li><a href="#!" class="text-white">Politique de confidentialité</a></li>
                        <li><a href="#!" class="text-white">Conditions d'utilisation</a></li>
                    </ul>
                </div>
                <div class="col-lg-6 col-md-12 mb-4 mb-md-0">
                    <h5 class="text-uppercase">Contact</h5>
                    <ul class="list-unstyled mb-0">
                        <li><a href="#!" class="text-white">Email: contact@refugecompagnonspalet.fr</a></li>
                        <li><a href="#!" class="text-white">Téléphone: 01 23 45 67 89</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <script src="./assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>