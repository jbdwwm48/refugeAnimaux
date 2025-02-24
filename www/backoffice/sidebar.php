<?php
function setActiveClass($pageName)
{
    $current_page = basename($_SERVER['PHP_SELF']);
    return $current_page === $pageName ? 'active' : '';
}
?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="index.php" class="brand-link">
        <span class="brand-text font-weight-light"> Gestion</span>
    </a>
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