<?php
$title = 'Mon Profil';
$pageTitle = 'Mon Profil';
ob_start();
?>
<div class="card">
    <div class="card-body">
        <form action="views/user/profile.php" method="post">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCsrfToken() ?>">
            
            <div class="mb-3">
                <label for="username" class="form-label">Nom d'utilisateur</label>
                <input type="text" class="form-control" id="username" value="<?= Security::escape($user['username']) ?>" disabled>
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= Security::escape($user['email']) ?>" required>
            </div>
            
            <h4 class="mt-4">Changer le mot de passe</h4>
            <div class="mb-3">
                <label for="current_password" class="form-label">Mot de passe actuel</label>
                <input type="password" class="form-control" id="current_password" name="current_password">
            </div>
            
            <div class="mb-3">
                <label for="new_password" class="form-label">Nouveau mot de passe</label>
                <input type="password" class="form-control" id="new_password" name="new_password">
            </div>
            
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirmer le mot de passe</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password">
            </div>
            
            <button type="submit" class="btn btn-primary">Mettre à jour</button>
        </form>
    </div>
</div>


<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/main.php';
?>