<?php
// app/controllers/UserController.php

class UserController {
    private $userModel;
    private $sessionModel;
    
    public function __construct() {
        // Vérification que l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Veuillez vous connecter pour accéder à cette page.';
            header('Location: /login');
            exit;
        }
        
        $this->userModel = new User();
        $this->sessionModel = new Session();
    }
    
    public function profile() {
        $user = $this->userModel->findById($_SESSION['user_id']);
        
        if (!$user) {
            $_SESSION['error'] = 'Erreur lors de la récupération de votre profil.';
            header('Location: /logout');
            exit;
        }
        
        require_once __DIR__ . '/../views/user/profile.php';
    }
    
    public function updateProfile() {
        // Vérification CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['error'] = 'Erreur de sécurité. Veuillez réessayer.';
            header('Location: /user/profile');
            exit;
        }
        
        $user = $this->userModel->findById($_SESSION['user_id']);
        
        if (!$user) {
            $_SESSION['error'] = 'Erreur lors de la récupération de votre profil.';
            header('Location: /logout');
            exit;
        }
        
        $email = $_POST['email'] ?? '';
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validation de l'email
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Adresse email invalide.';
            header('Location: /user/profile');
            exit;
        }
        
        // Vérification si l'email existe déjà (sauf pour l'utilisateur actuel)
        $existingEmail = $this->userModel->findByEmail($email);
        if ($existingEmail && $existingEmail['id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Cette adresse email est déjà utilisée.';
            header('Location: /user/profile');
            exit;
        }
        
        // Préparation des données à mettre à jour
        $userData = [
            'email' => $email
        ];
        
        // Mise à jour du mot de passe si demandé
        if (!empty($newPassword)) {
            // Vérification du mot de passe actuel
            if (empty($currentPassword) || !$this->userModel->verifyPassword($currentPassword, $user['password'])) {
                $_SESSION['error'] = 'Le mot de passe actuel est incorrect.';
                header('Location: /user/profile');
                exit;
            }
            
            // Vérification de la correspondance des nouveaux mots de passe
            if ($newPassword !== $confirmPassword) {
                $_SESSION['error'] = 'Les nouveaux mots de passe ne correspondent pas.';
                header('Location: /user/profile');
                exit;
            }
            
            // Vérification de la complexité du mot de passe
            if (strlen($newPassword) < 8 || 
                !preg_match('/[A-Z]/', $newPassword) || 
                !preg_match('/[a-z]/', $newPassword) || 
                !preg_match('/[0-9]/', $newPassword)) {
                $_SESSION['error'] = 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un chiffre.';
                header('Location: /user/profile');
                exit;
            }
            
            $userData['password'] = $newPassword;
        }
        
        // Mise à jour de l'utilisateur
        $this->userModel->update($_SESSION['user_id'], $userData);
        
        $_SESSION['success'] = 'Profil mis à jour avec succès.';
        header('Location: /user/profile');
        exit;
    }
    
    public function history() {
        $sessions = $this->sessionModel->getUserSessions($_SESSION['user_id']);
        require_once __DIR__ . '/../views/user/history.php';
    }
}