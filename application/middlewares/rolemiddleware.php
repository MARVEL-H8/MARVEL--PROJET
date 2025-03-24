<?php
// app/middlewares/RoleMiddleware.php

class RoleMiddleware {
    private $allowedRoles;
    
    public function __construct($roles) {
        $this->allowedRoles = is_array($roles) ? $roles : [$roles];
    }
    
    public function handle() {
        // Vérification si l'utilisateur est connecté
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
            $_SESSION['error'] = 'Veuillez vous connecter pour accéder à cette page.';
            header('Location: /login');
            exit;
        }
        
        // Vérification du rôle
        if (!in_array($_SESSION['role'], $this->allowedRoles)) {
            $_SESSION['error'] = 'Vous n\'avez pas les droits nécessaires pour accéder à cette page.';
            
            // Redirection vers la page appropriée selon le rôle
            if ($_SESSION['role'] === 'admin') {
                header('Location: /admin/dashboard');
            } else {
                header('Location: /user/profile');
            }
            exit;
        }
        
        return true;
    }
}