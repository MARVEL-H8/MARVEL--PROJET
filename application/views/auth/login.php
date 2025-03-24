<?php
// app/views/auth/login.php
$title = 'Connexion';
$pageTitle = 'Connexion';
ob_start();
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow">
            <div class="card-body p-5">
                <h3 class="card-title text-center mb-4">Connexion</h3>
                
                <form action="/login" method="post">
                    <input type="hidden" name="csrf_token" value="<?= Security::generateCsrfToken() ?>">
                    
                    <div class="mb-3">
                        <label for="identifier" class="form-label">Email ou nom d'utilisateur</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" class="form-control" id="identifier" name="identifier" required autofocus>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="form-label">Mot de passe</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Se connecter</button>
                    </div>
                </form>
                
                <div class="text-center mt-4">
                    <p>Vous n'avez pas de compte ? <a href="/register">Inscrivez-vous</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/main.php';
?>