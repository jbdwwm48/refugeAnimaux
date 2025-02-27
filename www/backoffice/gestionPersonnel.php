<?php
session_start();
require '../auth/initDb.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['utilisateur'])) {
    header('Location: ../index.php');
    exit;
}

// Récupérer les informations de l'utilisateur
$role_utilisateur    = strtolower($_SESSION['utilisateur']['poste']); 
$id_utilisateur      = $_SESSION['utilisateur']['id_personnel'];
$prenom_utilisateur  = $_SESSION['utilisateur']['prenom'];
$nom_utilisateur     = $_SESSION['utilisateur']['nom'];

// Fonction pour filtrer les données en fonction du rôle
function filtrerDonneesParRole($pdo, $role_utilisateur, $id_utilisateur = null)
{
    if ($role_utilisateur === 'soigneur') {
        $requete_personnels = $pdo->prepare("SELECT id_personnel, prenom, nom, poste, login FROM personnel WHERE id_personnel = :id_utilisateur");
        $requete_personnels->bindValue(':id_utilisateur', $id_utilisateur, PDO::PARAM_INT);
        $requete_personnels->execute();
        $personnel = $requete_personnels->fetch(PDO::FETCH_ASSOC);

        if (!$personnel) {
            return [];
        }

        // Récupération des animaux du soigneur
        $id_personnel = $personnel['id_personnel'];
        $requete_animaux = $pdo->prepare("SELECT a.nom, a.genre, a.numero, a.pays, a.date_naissance, a.date_arrivee, a.date_deces, a.historique, a.image, e.nom as espece FROM animal a 
            INNER JOIN s_occuper s ON a.id_animal = s.id_animal 
            INNER JOIN animal_espece ae ON a.id_animal = ae.id_animal 
            INNER JOIN espece e ON ae.id_espece = e.id_espece 
            WHERE s.id_personnel = :id_personnel");
        $requete_animaux->bindValue(':id_personnel', $id_personnel, PDO::PARAM_INT);
        $requete_animaux->execute();
        $animaux = $requete_animaux->fetchAll(PDO::FETCH_ASSOC);

        $personnel['animaux'] = $animaux;
        return [$personnel];
    } elseif ($role_utilisateur === 'cadre' || $role_utilisateur === 'administratif') {
        $requete_personnels = $pdo->prepare("SELECT id_personnel, prenom, nom, poste, login FROM personnel");
        $requete_personnels->execute();
        $personnels = $requete_personnels->fetchAll(PDO::FETCH_ASSOC);

        foreach ($personnels as &$personnel) {
            if ($personnel['poste'] === 'soigneur') {
                $id_personnel = $personnel['id_personnel'];
                $requete_animaux = $pdo->prepare("SELECT a.nom, a.genre, a.numero, a.pays, a.date_naissance, a.date_arrivee, a.date_deces, a.historique, a.image, e.nom as espece FROM animal a 
                    INNER JOIN s_occuper s ON a.id_animal = s.id_animal 
                    INNER JOIN animal_espece ae ON a.id_animal = ae.id_animal 
                    INNER JOIN espece e ON ae.id_espece = e.id_espece 
                    WHERE s.id_personnel = :id_personnel");
                $requete_animaux->bindValue(':id_personnel', $id_personnel, PDO::PARAM_INT);
                $requete_animaux->execute();
                $animaux = $requete_animaux->fetchAll(PDO::FETCH_ASSOC);
                $personnel['animaux'] = $animaux;
            }
        }
        return $personnels;
    }
    return [];
}

// Gérer la suppression
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Supprimer les liens entre le soigneur et les animaux
    $requete_delete_links = $pdo->prepare("DELETE FROM s_occuper WHERE id_personnel = :id_personnel");
    $requete_delete_links->bindValue(':id_personnel', $delete_id, PDO::PARAM_INT);
    $requete_delete_links->execute();

    // Supprimer le personnel
    $requete_delete = $pdo->prepare("DELETE FROM personnel WHERE id_personnel = :id_personnel");
    $requete_delete->bindValue(':id_personnel', $delete_id, PDO::PARAM_INT);
    $requete_delete->execute();

    header("Location: {$_SERVER['PHP_SELF']}");
    exit;
}

// Ajouter un membre
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    $prenom = $_POST['prenom'];
    $nom    = $_POST['nom'];
    $poste  = $_POST['poste'];
    $login  = $_POST['login'];

    $requete_ajouter = $pdo->prepare("INSERT INTO personnel (prenom, nom, poste, login) VALUES (:prenom, :nom, :poste, :login)");
    $requete_ajouter->bindValue(':prenom', $prenom);
    $requete_ajouter->bindValue(':nom', $nom);
    $requete_ajouter->bindValue(':poste', $poste);
    $requete_ajouter->bindValue(':login', $login);
    $requete_ajouter->execute();
    header("Location: {$_SERVER['PHP_SELF']}");
    exit;
}

// Mise à jour d'un membre
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier'])) {
    $id_personnel = $_POST['id_personnel'];
    $prenom       = $_POST['prenom'];
    $nom          = $_POST['nom'];
    $poste        = $_POST['poste'];
    $login        = $_POST['login'];
    $mot_de_passe = $_POST['mot_de_passe'];

    if (!empty($mot_de_passe)) {
        // Hachage du mot de passe si un nouveau est fourni
        $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
        $requete_modifier = $pdo->prepare("UPDATE personnel SET prenom = :prenom, nom = :nom, poste = :poste, login = :login, mot_de_passe = :mot_de_passe WHERE id_personnel = :id_personnel");
        $requete_modifier->bindValue(':mot_de_passe', $mot_de_passe_hash);
    } else {
        $requete_modifier = $pdo->prepare("UPDATE personnel SET prenom = :prenom, nom = :nom, poste = :poste, login = :login WHERE id_personnel = :id_personnel");
    }
    $requete_modifier->bindValue(':prenom', $prenom);
    $requete_modifier->bindValue(':nom', $nom);
    $requete_modifier->bindValue(':poste', $poste);
    $requete_modifier->bindValue(':login', $login);
    $requete_modifier->bindValue(':id_personnel', $id_personnel, PDO::PARAM_INT);
    $requete_modifier->execute();

    header("Location: {$_SERVER['PHP_SELF']}");
    exit;
}

// Récupérer les personnels
$personnels = filtrerDonneesParRole($pdo, $role_utilisateur, $id_utilisateur);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion du Personnel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light" style="background-color: #f8f9fa;">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
            <img src="https://mdbootstrap.com/img/Photos/new-templates/animal-shelter/logo.png" height="70" alt="logo du refuge" loading="lazy" />                
            </a>
            <div class="d-flex">
                <a href="dashboard.php" class="btn btn-outline-primary me-2">Dashboard</a>
                <a href="logout.php" class="btn btn-outline-danger">Déconnexion</a>
            </div>
        </div>
    </nav>
    <div class="container mt-5">
        <h1 class="text-center my-4">Gestion du Personnel</h1>
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h2 class="card-title">Liste du Personnel</h2>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Prénom</th>
                                <th>Nom</th>
                                <th>Poste</th>
                                <th>Login</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($personnels as $personnel) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($personnel['prenom']) ?></td>
                                    <td><?= htmlspecialchars($personnel['nom']) ?></td>
                                    <td><?= htmlspecialchars($personnel['poste']) ?></td>
                                    <td><?= htmlspecialchars($personnel['login']) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modifierModal"
                                            data-id="<?= $personnel['id_personnel'] ?>"
                                            data-prenom="<?= htmlspecialchars($personnel['prenom']) ?>"
                                            data-nom="<?= htmlspecialchars($personnel['nom']) ?>"
                                            data-poste="<?= htmlspecialchars($personnel['poste']) ?>"
                                            data-login="<?= htmlspecialchars($personnel['login']) ?>">
                                            Modifier
                                        </button>
                                        <a href="?delete_id=<?= $personnel['id_personnel'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Confirmer la suppression ?')">Supprimer</a>
                                        <?php if ($personnel['poste'] === 'soigneur'): ?>
                                            <button class="btn btn-sm btn-info" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#animauxModal"
                                                data-animaux="<?= htmlspecialchars(json_encode($personnel['animaux'])) ?>">
                                                Voir Animaux
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <button class="btn btn-success mt-3" data-bs-toggle="modal" data-bs-target="#ajouterModal">Ajouter un membre</button>
            </div>
        </div>
    </div>

    <!-- Modal pour voir les animaux -->
    <div class="modal fade" id="animauxModal" tabindex="-1" aria-labelledby="animauxModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Animaux du Soigneur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <div id="animauxList" class="row g-3">
                        <!-- Les animaux seront affichés ici -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de modification -->
    <div class="modal fade" id="modifierModal" tabindex="-1" aria-labelledby="modifierModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier un Membre</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="prenom" class="form-label">Prénom</label>
                            <input type="text" class="form-control" id="prenom" name="prenom" required>
                        </div>
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom</label>
                            <input type="text" class="form-control" id="nom" name="nom" required>
                        </div>
                        <div class="mb-3">
                            <label for="poste" class="form-label">Poste</label>
                            <input type="text" class="form-control" id="poste" name="poste" required>
                        </div>
                        <div class="mb-3">
                            <label for="login" class="form-label">Login</label>
                            <input type="text" class="form-control" id="login" name="login" required>
                        </div>
                        <div class="mb-3">
                            <label for="mot_de_passe" class="form-label">Mot de passe (optionnel)</label>
                            <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe">
                        </div>
                        <input type="hidden" name="id_personnel" id="id_personnel">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        <button type="submit" name="modifier" class="btn btn-primary">Modifier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Afficher les animaux dans le modal
        var animauxModal = document.getElementById('animauxModal');
        animauxModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var animaux = JSON.parse(button.getAttribute('data-animaux'));
            var animauxList = document.getElementById('animauxList');
            animauxList.innerHTML = '';

            animaux.forEach(function (animal) {
                var col = document.createElement('div');
                col.classList.add('col-md-6');
                col.innerHTML = `
                    <div class="card">
                        <img src="${animal.image || 'default.jpg'}" class="card-img-top" alt="Image de l'animal">
                        <div class="card-body">
                            <h5 class="card-title">${animal.nom}</h5>
                            <p class="card-text">
                                <strong>Espèce</strong>: ${animal.espece}<br>
                                <strong>Genre</strong>: ${animal.genre}<br>
                                <strong>Naissance</strong>: ${animal.date_naissance}<br>
                                <strong>Arrivée</strong>: ${animal.date_arrivee}<br>
                                <strong>Décès</strong>: ${animal.date_deces || 'Non décédé'}<br>
                                <strong>Historique</strong>: ${animal.historique}
                            </p>
                        </div>
                    </div>
                `;
                animauxList.appendChild(col);
            });
        });

        // Remplir les champs de modification du personnel
        var modifierModal = document.getElementById('modifierModal');
        modifierModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');
            var prenom = button.getAttribute('data-prenom');
            var nom = button.getAttribute('data-nom');
            var poste = button.getAttribute('data-poste');
            var login = button.getAttribute('data-login');

            document.getElementById('id_personnel').value = id;
            document.getElementById('prenom').value = prenom;
            document.getElementById('nom').value = nom;
            document.getElementById('poste').value = poste;
            document.getElementById('login').value = login;
        });
    </script>
</body>
</html>