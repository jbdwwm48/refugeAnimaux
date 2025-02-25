<?php 
function setActiveClass($pageName)
{
    $current_page = basename($_SERVER['PHP_SELF']);
    return $current_page === $pageName ? 'active' : '';
}
?>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark d-lg-none">
    <a class="navbar-brand" href="index.php">Gestion</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
</nav>

<!-- Sidebar Desktop -->
<aside class="main-sidebar sidebar-dark-primary elevation-4 d-none d-lg-block">
    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link <?= setActiveClass('dashboard.php') ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="gestionPersonnel.php" class="nav-link <?= setActiveClass('gestionPersonnel.php') ?>">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Gestion du personnel</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="gestionAnimaux.php" class="nav-link <?= setActiveClass('gestionAnimaux.php') ?>">
                        <i class="nav-icon fas fa-dog"></i>
                        <p>Liste des animaux</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="gestionCages.php" class="nav-link <?= setActiveClass('gestionCages.php') ?>">
                        <i class="nav-icon fas fa-box"></i>
                        <p>Cages</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>

<!-- Sidebar Mobile -->
<div class="collapse d-lg-none" id="sidebarMenu">
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link <?= setActiveClass('dashboard.php') ?>">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="gestionPersonnel.php" class="nav-link <?= setActiveClass('gestionPersonnel.php') ?>">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Gestion du personnel</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="gestionAnimaux.php" class="nav-link <?= setActiveClass('gestionAnimaux.php') ?>">
                            <i class="nav-icon fas fa-dog"></i>
                            <p>Liste des animaux</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="gestionCages.php" class="nav-link <?= setActiveClass('gestionCages.php') ?>">
                            <i class="nav-icon fas fa-box"></i>
                            <p>Cages</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>
</div>

<!-- Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const toggler = document.querySelector(".navbar-toggler");
        const sidebar = document.querySelector("#sidebarMenu");


        toggler.addEventListener("click", function () {
            sidebar.classList.toggle("show");
        });

        document.addEventListener("click", function (event) {
            if (!sidebar.contains(event.target) && !toggler.contains(event.target)) {
                sidebar.classList.remove("show");
            }
        });
    });
</script>

<style>
    @media (max-width: 768px) {
        #sidebarMenu {
            position: fixed;
            top: 56px; 
            left: 0;
            width: 250px;
            height: calc(100% - 56px); 
            background-color: #343a40;
            z-index: 1050;
            overflow-y: auto;
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
        }

        #sidebarMenu.show {
            transform: translateX(0);
        }

        #sidebarMenu.show ~ .container {
            opacity: 0.5;
        }

        .sidebar {
            padding-top: 0;
        }
    }
</style>
