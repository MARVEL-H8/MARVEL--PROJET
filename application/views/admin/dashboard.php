<?php
// app/views/admin/dashboard.php
$title = 'Tableau de bord';
$pageTitle = 'Tableau de bord administrateur';
ob_start();
?>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card bg-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1">Utilisateurs total</h6>
                        <h2 class="display-4 mb-0"><?= $stats['total_users'] ?? 0 ?></h2>
                    </div>
                    <i class="bi bi-people display-4"></i>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="small text-white stretched-link" href="/admin/users">Voir détails</a>
                <i class="bi bi-chevron-right text-white"></i>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card bg-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1">Utilisateurs actifs</h6>
                        <h2 class="display-4 mb-0"><?= $stats['active_users'] ?? 0 ?></h2>
                    </div>
                    <i class="bi bi-person-check display-4"></i>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="small text-white stretched-link" href="/admin/users?status=active">Voir détails</a>
                <i class="bi bi-chevron-right text-white"></i>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card bg-warning text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1">Connexions aujourd'hui</h6>
                        <h2 class="display-4 mb-0"><?= $stats['today_logins'] ?? 0 ?></h2>
                    </div>
                    <i class="bi bi-clock-history display-4"></i>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a class="small text-white stretched-link" href="/admin/logs">Voir détails</a>
                <i class="bi bi-chevron-right text-white"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Dernières connexions</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Utilisateur</th>
                                <th>Date</th>
                                <th>Adresse IP</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_logs as $log): ?>
                            <tr>
                                <td><?= Security::escape($log['username']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($log['login_time'])) ?></td>
                                <td><?= Security::escape($log['ip_address']) ?></td>
                                <td>
                                    <span class="badge bg-success">Succès</span>
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

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/main.php';
?>