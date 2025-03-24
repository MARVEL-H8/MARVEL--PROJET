<?php
$title = 'Inscription';
$pageTitle = 'Créer un compte';
ob_start();
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-body">
                <form action="/register" method="post" class="needs-validation" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= Security::generateCsrfToken() ?>">

                    <div class="mb-3">
                        <label for="username" class="form-label">Nom d'utilisateur</label>
                        <input type="text" class="form-control" id="username" name="username" 
                               required pattern="^[a-zA-Z0-9_-]{3,20}$" 
                               value="<?= isset($_POST['username']) ? Security::escape($_POST['username']) : '' ?>">
                        <div class="invalid-feedback">
                            Le nom d'utilisateur doit contenir entre 3 et 20 caractères (lettres, chiffres, - et _)
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Adresse email</label>
                        <input type="email" class="form-control" id="email" name="email" required
                               value="<?= isset($_POST['email']) ? Security::escape($_POST['email']) : '' ?>">
                        <div class="invalid-feedback">
                            Veuillez saisir une adresse email valide
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password" 
                               required minlength="8">
                        <div class="invalid-feedback">
                            Le mot de passe doit contenir au moins 8 caractères
                        </div>
                        <div class="form-text">
                            Le mot de passe doit contenir au moins 8 caractères, une majuscule, 
                            une minuscule et un chiffre
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

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                        <label class="form-check-label" for="terms">
                            J'accepte les conditions d'utilisation
                        </label>
                        <div class="invalid-feedback">
                            Vous devez accepter les conditions d'utilisation
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">S'inscrire</button>
                    </div>

                    <div class="text-center mt-3">
                        <p>Déjà inscrit ? <a href="/auth/login">Connectez-vous</a></p>
                    </div>
                </form>
            </div>
        </div>
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
        
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        
        if (password.value !== confirmPassword.value) {
            event.preventDefault();
            confirmPassword.setCustomValidity('Les mots de passe ne correspondent pas');
        } else {
            confirmPassword.setCustomValidity('');
        }
        
        // Validation complexité mot de passe
        const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
        if (!passwordRegex.test(password.value)) {
            event.preventDefault();
            password.setCustomValidity('Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un chiffre');
        } else {
            password.setCustomValidity('');
        }
        
        form.classList.add('was-validated');
    });
});
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../main.php';
?>