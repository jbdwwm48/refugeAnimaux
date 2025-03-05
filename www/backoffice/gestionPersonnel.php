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
    // [Même fonction que dans votre code original]
    if ($role_utilisateur === 'soigneur') {
        $requete_personnels = $pdo->prepare("SELECT id_personnel, prenom, nom, poste, login FROM personnel WHERE id_personnel = :id_utilisateur");
        $requete_personnels->bindValue(':id_utilisateur', $id_utilisateur, PDO::PARAM_INT);
        $requete_personnels->execute();
        $personnel = $requete_personnels->fetch(PDO::FETCH_ASSOC);

        if (!$personnel) {
            return [];
        }

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
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    try {
        $pdo->beginTransaction();
        $requete_delete_links = $pdo->prepare("DELETE FROM s_occuper WHERE id_personnel = :id_personnel");
        $requete_delete_links->bindValue(':id_personnel', $delete_id, PDO::PARAM_INT);
        $requete_delete_links->execute();

        $requete_delete = $pdo->prepare("DELETE FROM personnel WHERE id_personnel = :id_personnel");
        $requete_delete->bindValue(':id_personnel', $delete_id, PDO::PARAM_INT);
        $requete_delete->execute();

        $pdo->commit();
        $_SESSION['success'] = "Le membre a été supprimé avec succès.";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Erreur lors de la suppression : " . $e->getMessage();
    }
    header("Location: gestionPersonne.php");
    exit;
}

// Ajouter un membre
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    $prenom = $_POST['prenom'];
    $nom    = $_POST['nom'];
    $poste  = $_POST['poste'];
    $login  = $_POST['login'];
    $mot_de_passe = $_POST['mot_de_passe'];

    if (!empty($prenom) && !empty($nom) && !empty($poste) && !empty($login) && !empty($mot_de_passe)) {
        $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
        try {
            $requete_ajouter = $pdo->prepare("INSERT INTO personnel (prenom, nom, poste, login, mot_de_passe) VALUES (:prenom, :nom, :poste, :login, :mot_de_passe)");
            $requete_ajouter->execute([
                ':prenom' => $prenom,
                ':nom' => $nom,
                ':poste' => $poste,
                ':login' => $login,
                ':mot_de_passe' => $mot_de_passe_hash
            ]);
            $_SESSION['success'] = "Membre ajouté avec succès.";
        } catch (Exception $e) {
            $_SESSION['error'] = "Erreur lors de l'ajout : " . $e->getMessage();
        }
        header("Location: gestionPersonne.php");
        exit;
    } else {
        $_SESSION['error'] = "Veuillez remplir tous les champs.";
    }
}

// Mise à jour d'un membre
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier'])) {
    $id_personnel = $_POST['id_personnel'];
    $prenom       = $_POST['prenom'];
    $nom          = $_POST['nom'];
    $poste        = $_POST['poste'];
    $login        = $_POST['login'];
    $mot_de_passe = $_POST['mot_de_passe'];

    try {
        if (!empty($mot_de_passe)) {
            $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
            $requete_modifier = $pdo->prepare("UPDATE personnel SET prenom = :prenom, nom = :nom, poste = :poste, login = :login, mot_de_passe = :mot_de_passe WHERE id_personnel = :id_personnel");
            $requete_modifier->bindValue(':mot_de_passe', $mot_de_passe_hash);
        } else {
            $requete_modifier = $pdo->prepare("UPDATE personnel SET prenom = :prenom, nom = :nom, poste = :poste, login = :login WHERE id_personnel = :id_personnel");
        }
        $requete_modifier->execute([
            ':prenom' => $prenom,
            ':nom' => $nom,
            ':poste' => $poste,
            ':login' => $login,
            ':id_personnel' => $id_personnel
        ]);
        $_SESSION['success'] = "Membre modifié avec succès.";
    } catch (Exception $e) {
        $_SESSION['error'] = "Erreur lors de la modification : " . $e->getMessage();
    }
    header("Location: gestionPersonne.php");
    exit;
}

// Récupérer les personnels
$personnels = filtrerDonneesParRole($pdo, $role_utilisateur, $id_utilisateur);

// Fonction pour générer des liens de tri (simplifiée ici, peut être étendue)
function getSortLink($column, $current_sort, $current_order)
{
    $new_order = ($current_sort === $column && $current_order === 'ASC') ? 'desc' : 'asc';
    return "?sort=$column&order=$new_order";
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion du Personnel</title>
    <style>
        .table-responsive {
            max-width: 100%;
            margin: auto;
        }

        .table-sm td,
        .table-sm th {
            padding: 0.5rem;
            font-size: 0.9rem;
        }

        .navbar-custom {
            background-color: rgb(72, 149, 182);
            color: white;
        }

        .animal-details {
            background-color: #f8f9fa;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
        }

        .lightbox {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .lightbox-content {
            background: white;
            padding: 20px;
            border-radius: 5px;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            position: relative;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 1.5rem;
            cursor: pointer;
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="hold-transition sidebar-mini">
    <?php include('../nav.php'); ?>

    <div class="wrapper">
        <?php include('./sidebar.php') ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <h1 class="text-center my-4">Gestion du Personnel</h1>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <?php if (isset($_SESSION['success'])) : ?>
                        <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['error'])) : ?>
                        <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <div class="row justify-content-center">
                        <div class="col-md-10">
                            <div class="card shadow">
                                <div class="card-header bg-primary text-white">
                                    <h2 class="card-title">Liste du Personnel</h2>
                                    <div class="container-fluid mb-1">
                                        <div class="d-flex justify-content-end gap-4">
                                            <div class="row justify-content-end">
                                                <div class="col-auto">
                                                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#ajouterModal">
                                                        <i class="fas fa-plus"></i> Ajouter un membre
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered table-striped">
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
                                                            <button class="btn btn-sm btn-warning edit-personnel"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#modifierModal"
                                                                data-id="<?= $personnel['id_personnel'] ?>"
                                                                data-prenom="<?= htmlspecialchars($personnel['prenom']) ?>"
                                                                data-nom="<?= htmlspecialchars($personnel['nom']) ?>"
                                                                data-poste="<?= htmlspecialchars($personnel['poste']) ?>"
                                                                data-login="<?= htmlspecialchars($personnel['login']) ?>">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-danger delete-personnel"
                                                                data-id="<?= $personnel['id_personnel'] ?>"
                                                                data-nom="<?= htmlspecialchars($personnel['prenom'] . ' ' . $personnel['nom']) ?>">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                            <?php if ($personnel['poste'] === 'soigneur') : ?>
                                                                <button class="btn btn-sm btn-info"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#animauxModal"
                                                                    data-animaux="<?= htmlspecialchars(json_encode($personnel['animaux'])) ?>">
                                                                    <i class="fas fa-paw"></i>
                                                                </button>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- Modal pour ajouter un membre -->
    <div class="modal fade" id="ajouterModal" tabindex="-1" aria-labelledby="ajouterModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ajouterModalLabel">Ajouter un membre</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
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
                            <label for="mot_de_passe" class="form-label">Mot de passe</label>
                            <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" name="ajouter" class="btn btn-primary">Ajouter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pour modifier un membre -->
    <div class="modal fade" id="modifierModal" tabindex="-1" aria-labelledby="modifierModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modifierModalLabel">Modifier un membre</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" id="edit_id_personnel" name="id_personnel">
                        <div class="mb-3">
                            <label for="edit_prenom" class="form-label">Prénom</label>
                            <input type="text" class="form-control" id="edit_prenom" name="prenom" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_nom" class="form-label">Nom</label>
                            <input type="text" class="form-control" id="edit_nom" name="nom" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_poste" class="form-label">Poste</label>
                            <input type="text" class="form-control" id="edit_poste" name="poste" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_login" class="form-label">Login</label>
                            <input type="text" class="form-control" id="edit_login" name="login" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_mot_de_passe" class="form-label">Mot de passe (optionnel)</label>
                            <input type="password" class="form-control" id="edit_mot_de_passe" name="mot_de_passe">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" name="modifier" class="btn btn-primary">Enregistrer</button>
                        </div>
                    </form>
                </div>
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
                    <div id="animauxList" class="row g-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmation de suppression -->
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirmation de suppression</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Êtes-vous sûr de vouloir supprimer le membre : <strong id="personnelNom"></strong> ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <a id="confirmDeleteButton" href="#" class="btn btn-danger">Supprimer</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gérer la suppression
            document.querySelectorAll('.delete-personnel').forEach(button => {
                button.addEventListener('click', function() {
                    const personnelId = this.getAttribute('data-id');
                    const personnelNom = this.getAttribute('data-nom');
                    const deleteUrl = `?delete=${personnelId}`;

                    document.getElementById('confirmDeleteButton').href = deleteUrl;
                    document.getElementById('personnelNom').textContent = personnelNom;
                    const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
                    deleteModal.show();
                });
            });

            // Gérer la modification
            document.querySelectorAll('.edit-personnel').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const prenom = this.getAttribute('data-prenom');
                    const nom = this.getAttribute('data-nom');
                    const poste = this.getAttribute('data-poste');
                    const login = this.getAttribute('data-login');

                    document.getElementById('edit_id_personnel').value = id;
                    document.getElementById('edit_prenom').value = prenom;
                    document.getElementById('edit_nom').value = nom;
                    document.getElementById('edit_poste').value = poste;
                    document.getElementById('edit_login').value = login;
                });
            });

            // Gérer l'affichage des animaux
            var animauxModal = document.getElementById('animauxModal');
            animauxModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var animaux = JSON.parse(button.getAttribute('data-animaux'));
                var animauxList = document.getElementById('animauxList');
                animauxList.innerHTML = '';

                animaux.forEach(function(animal) {
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
        });
    </script>
</body>

</html>