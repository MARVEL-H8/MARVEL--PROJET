<?php
require_once __DIR__ . '/../../helpers/security.php';

// app/views/layouts/main.php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Gestionnaire de Clients Sécurisé' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Ajout DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <style>
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 48px 0 0;
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
        }
        
        .sidebar-sticky {
            position: relative;
            top: 0;
            height: calc(100vh - 48px);
            padding-top: .5rem;
            overflow-x: hidden;
            overflow-y: auto;
        }
        
        .nav-link {
            font-weight: 500;
            color: #333;
        }
        
        .nav-link.active {
            color: #007bff;
        }
    </style>
</head>
<body>
    <header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-2 shadow">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="/">Gestionnaire Clients</a>
        <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="navbar-nav">
                <div class="nav-item text-nowrap d-flex align-items-center">
                    <span class="nav-link text-white px-3">
                        <i class="bi bi-person-circle"></i> <?= Security::escape($_SESSION['username']) ?>
                    </span>
                    <a class="nav-link px-3" href="/logout">
                        <i class="bi bi-box-arrow-right"></i> Déconnexion
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </header>

    <div class="container-fluid">
        <div class="row">
            <?php if (isset($_SESSION['user_id'])): ?>
                <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                    <div class="position-sticky sidebar-sticky pt-3">
                        <ul class="nav flex-column">
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                <!-- Menu Administrateur -->
                                <li class="nav-item">
                                    <a class="nav-link <?= $_SERVER['REQUEST_URI'] === '/admin/dashboard' ? 'active' : '' ?>" href="/admin/dashboard">
                                        <i class="bi bi-speedometer2"></i> Tableau de bord
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/users') !== false ? 'active' : '' ?>" href="/admin/users">
                                        <i class="bi bi-people"></i> Gestion utilisateurs
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= $_SERVER['REQUEST_URI'] === '/admin/logs' ? 'active' : '' ?>" href="/admin/logs">
                                        <i class="bi bi-list-check"></i> Journaux de connexion
                                    </a>
                                </li>
                            <?php elseif (isset($_SESSION['role'])): ?>
                                <!-- Menu Client -->
                                <li class="nav-item">
                                    <a class="nav-link <?= $_SERVER['REQUEST_URI'] === '/user/profile' ? 'active' : '' ?>" href="/user/profile">
                                        <i class="bi bi-person"></i> Mon profil
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= $_SERVER['REQUEST_URI'] === '/user/history' ? 'active' : '' ?>" href="/user/history">
                                        <i class="bi bi-clock-history"></i> Historique connexions
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </nav>
            <?php endif; ?>

            <main class="<?= isset($_SESSION['user_id']) ? 'col-md-9 ms-sm-auto col-lg-10 px-md-4' : 'col-12' ?>">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><?= isset($pageTitle) ? Security::escape($pageTitle) : 'Accueil' ?></h1>
                </div>
                
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= Security::escape($_SESSION['success']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= Security::escape($_SESSION['error']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
                
                <?= isset($content) ? $content : '' ?>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        // Auto-dismiss des alertes après 5 secondes
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });
    </script>
</body>
</html>