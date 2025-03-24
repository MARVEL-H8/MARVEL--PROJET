<?php
$title = 'Historique des connexions';
$pageTitle = 'Mon historique de connexions';
ob_start();
?>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Historique des connexions</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="historyTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Date de connexion</th>
                        <th>Date de déconnexion</th>
                        <th>Adresse IP</th>
                        <th>Navigateur</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sessions as $session): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i:s', strtotime($session['login_time'])) ?></td>
                            <td>
                                <?= $session['logout_time'] 
                                    ? date('d/m/Y H:i:s', strtotime($session['logout_time']))
                                    : '<span class="badge bg-success">Session active</span>' 
                                ?>
                            </td>
                            <td><?= Security::escape($session['ip_address']) ?></td>
                            <td><?= Security::escape($session['user_agent']) ?></td>
                            <td>
                                <?php if ($session['status'] === 'success'): ?>
                                    <span class="badge bg-success">Succès</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Échec</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    new DataTable('#historyTable', {
        order: [[0, 'desc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json'
        },
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Tous"]],
        responsive: true
    });
});
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/main.php';
?>