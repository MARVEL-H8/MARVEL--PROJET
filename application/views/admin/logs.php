<?php
$title = 'Journaux de connexion';
$pageTitle = 'Journaux de connexion';
ob_start();
?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold">Historique des connexions</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="logsTable">
                <thead>
                    <tr>
                        <th>Utilisateur</th>
                        <th>Date connexion</th>
                        <th>Date déconnexion</th>
                        <th>Adresse IP</th>
                        <th>Navigateur</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?= Security::escape($log['username']) ?></td>
                        <td><?= date('d/m/Y H:i:s', strtotime($log['login_time'])) ?></td>
                        <td>
                            <?= $log['logout_time'] 
                                ? date('d/m/Y H:i:s', strtotime($log['logout_time'])) 
                                : '<span class="badge bg-success">Session active</span>' 
                            ?>
                        </td>
                        <td><?= Security::escape($log['ip_address']) ?></td>
                        <td><?= Security::escape($log['user_agent']) ?></td>
                        <td>
                            <span class="badge bg-<?= $log['status'] === 'success' ? 'success' : 'danger' ?>">
                                <?= Security::escape($log['status']) ?>
                            </span>
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
    const table = new DataTable('#logsTable', {
        order: [[1, 'desc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json'
        }
    });
});
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/main.php';
?>