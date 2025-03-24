<?php
// app/middlewares/AuthMiddleware.php

class AuthMiddleware {
    public function handle() {
        // Vérification si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            // Stockage de l'URL demandée pour redirection après connexion
            $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
            
            $_SESSION['error'] = 'Veuillez vous connecter pour accéder à cette page.';
            header('Location: /login');
            exit;
        }
        
        // Vérification si la session est toujours valide en base de données
        $sessionModel = new Session();
        $currentSession = $sessionModel->getCurrentSession();
        
        if (!$currentSession || $currentSession['logout_time']) {
            // Session expirée ou fermée
            $_SESSION = [];
            session_destroy();
            
            $_SESSION['error'] = 'Votre session a expiré. Veuillez vous reconnecter.';
            header('Location: /login');
            exit;
        }
        
        // Vérification si l'utilisateur est toujours actif
        $userModel = new User();
        $user = $userModel->findById($_SESSION['user_id']);
        
        if (!$user || $user['status'] !== 'active') {
            // Fermeture de la session en base de données
            $sessionModel->closeSession($_SESSION['session_id']);
            
            // Déconnexion
            $_SESSION = [];
            session_destroy();
            
            $_SESSION['error'] = 'Votre compte a été désactivé. Veuillez contacter l\'administrateur.';
            header('Location: /login');
            exit;
        }
        
        return true;
    }
}