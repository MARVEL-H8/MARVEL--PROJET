<?php
$title = isset($user) ? 'Modifier un utilisateur' : 'Créer un utilisateur';
$pageTitle = isset($user) ? 'Modifier un utilisateur' : 'Créer un utilisateur';
ob_start();
?>

<div class="card shadow">
    <div class="card-body">
        <form action="<?= isset($user) ? "/admin/users/edit/{$user['id']}" : '/admin/users/create' ?>" 
              method="post" class="needs-validation" novalidate>
            
            <input type="hidden" name="csrf_token" value="<?= Security::generateCsrfToken() ?>">
            
            <div class="mb-3">
                <label for="username" class="form-label">Nom d'utilisateur</label>
                <input type="text" class="form-control" id="username" name="username" 
                       value="<?= isset($user) ? Security::escape($user['username']) : '' ?>" 
                       required pattern="^[a-zA-Z0-9_-]{3,20}$">
                <div class="invalid-feedback">
                    Le nom d'utilisateur doit contenir entre 3 et 20 caractères (lettres, chiffres, - et _)
                </div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" 
                       value="<?= isset($user) ? Security::escape($user['email']) : '' ?>" 
                       required>
                <div class="invalid-feedback">
                    Veuillez saisir une adresse email valide
                </div>
            </div>

            <div class="mb-3">
                <label for="role" class="form-label">Rôle</label>
                <select class="form-select" id="role" name="role" required>
                    <option value="">Sélectionner un rôle</option>
                    <option value="admin" <?= (isset($user) && $user['role'] === 'admin') ? 'selected' : '' ?>>
                        Administrateur
                    </option>
                    <option value="client" <?= (isset($user) && $user['role'] === 'client') ? 'selected' : '' ?>>
                        Client
                    </option>
                </select>
            </div>

            <?php if (!isset($user)): ?>
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" class="form-control" id="password" name="password" 
                       required minlength="8">
                <div class="invalid-feedback">
                    Le mot de passe doit contenir au moins 8 caractères
                </div>
            </div>

            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirmer le mot de passe</label>
                <input type="password" class="form-control" id="confirm_password" 
                       name="confirm_password" required>
                <div class="invalid-feedback">
                    Les mots de passe ne correspondent pas
                </div>
            </div>
            <?php endif; ?>

            <div class="mb-3">
                <label for="status" class="form-label">Statut</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="active" <?= (isset($user) && $user['status'] === 'active') ? 'selected' : '' ?>>
                        Actif
                    </option>
                    <option value="inactive" <?= (isset($user) && $user['status'] === 'inactive') ? 'selected' : '' ?>>
                        Inactif
                    </option>
                </select>
            </div>

            <div class="d-flex justify-content-between">
                <a href="/admin/users" class="btn btn-secondary">Retour</a>
                <button type="submit" class="btn btn-primary">
                    <?= isset($user) ? 'Mettre à jour' : 'Créer' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.needs-validation');
    
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        
        if (document.getElementById('password') && 
            document.getElementById('password').value !== 
            document.getElementById('confirm_password').value) {
            event.preventDefault();
            document.getElementById('confirm_password').setCustomValidity('Les mots de passe ne correspondent pas');
        }
        
        form.classList.add('was-validated');
    });
});
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/main.php';
?>