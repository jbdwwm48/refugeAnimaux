<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Utilisateurs - AdminLTE</title>

    <!-- Bootstrap & AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <!-- Styles personnalisés de votre collègue -->
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
    </style>
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <?php include('../nav.php'); ?>

        <!-- Sidebar -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="#" class="brand-link">
                <span class="brand-text font-weight-light">AdminLTE</span>
            </a>
            <div class="sidebar">
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column">
                        <li class="nav-item">
                            <a href="#" class="nav-link active">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Gestion des utilisateurs</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Contenu principal -->
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <h1 class="text-center">Gestion des utilisateurs</h1>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <!-- Carte avec tableau -->
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Liste des utilisateurs</h3>
                                    <button class="btn btn-success float-end" data-bs-toggle="modal" data-bs-target="#addUserModal">
                                        Ajouter un utilisateur
                                    </button>
                                </div>
                                <div class="card-body">
                                    <table id="userTable" class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Nom</th>
                                                <th>Email</th>
                                                <th>Rôle</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="userTableBody">
                                            <!-- Les utilisateurs seront injectés ici -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Modal : Détails utilisateur -->
        <div class="modal fade" id="userDetailModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Détails de l'utilisateur</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Nom :</strong> <span id="detailName"></span></p>
                        <p><strong>Email :</strong> <span id="detailEmail"></span></p>
                        <p><strong>Rôle :</strong> <span id="detailRole"></span></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal : Ajouter un utilisateur -->
        <div class="modal fade" id="addUserModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Ajouter un utilisateur</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="addUserForm">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nom</label>
                                <input type="text" class="form-control" id="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="role" class="form-label">Rôle</label>
                                <select class="form-select" id="role">
                                    <option>Admin</option>
                                    <option>Utilisateur</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Ajouter</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <?php include('../footer.php'); ?>
    </div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="script.js"></script>
</body>

</html>