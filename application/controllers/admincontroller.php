<?php
// app/controllers/AdminController.php

class AdminController {
    private $userModel;
    private $sessionModel;
    
    public function __construct() {
        // Vérification que l'utilisateur est administrateur
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = 'Accès non autorisé.';
            header('Location: /login');
            exit;
        }
        
        $this->userModel = new User();
        $this->sessionModel = new Session();
    }
    
    public function dashboard() {
        // Comptage des utilisateurs
        $db = Database::getInstance();
        $stats = [
            'total_users' => $db->fetch("SELECT COUNT(*) as count FROM users")['count'],
            'active_users' => $db->fetch("SELECT COUNT(*) as count FROM users WHERE status = 'active'")['count'],
            'inactive_users' => $db->fetch("SELECT COUNT(*) as count FROM users WHERE status = 'inactive'")['count'],
            'admin_users' => $db->fetch("SELECT COUNT(*) as count FROM users WHERE role_id = (SELECT id FROM roles WHERE name = 'admin')")['count'],
            'client_users' => $db->fetch("SELECT COUNT(*) as count FROM users WHERE role_id = (SELECT id FROM roles WHERE name = 'client')")['count'],
            'active_sessions' => $db->fetch("SELECT COUNT(*) as count FROM sessions WHERE logout_time IS NULL")['count']
        ];
        
        // Récupération des 10 dernières connexions
        $recentSessions = $this->sessionModel->getAllSessions(10);
        
        require_once __DIR__ . '/../views/admin/dashboard.php';
    }
    
    public function users() {
        $users = $this->userModel->getAll();
        require_once __DIR__ . '/../views/admin/users.php';
    }
    
    public function logs() {
        $sessions = $this->sessionModel->getAllSessions();
        require_once __DIR__ . '/../views/admin/logs.php';
    }
    
    public function showCreateUserForm() {
        // Récupération des rôles disponibles
        $db = Database::getInstance();
        $roles = $db->fetchAll("SELECT * FROM roles");
        
        require_once __DIR__ . '/../views/admin/create_user.php';
    }
    
    public function createUser() {
        // Vérification CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['error'] = 'Erreur de sécurité. Veuillez réessayer.';
            header('Location: /admin/users/create');
            exit;
        }
        
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $roleId = $_POST['role_id'] ?? '';
        $status = $_POST['status'] ?? 'active';
        
        // Validation des champs
        if (empty($username) || empty($email) || empty($password) || empty($roleId)) {
            $_SESSION['error'] = 'Tous les champs sont obligatoires.';
            header('Location: /admin/users/create');
            exit;
        }
        
        // Validation de l'email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Adresse email invalide.';
            header('Location: /admin/users/create');
            exit;
        }
        
        // Vérification si l'email ou le nom d'utilisateur existe déjà
        if ($this->userModel->findByEmail($email)) {
            $_SESSION['error'] = 'Cette adresse email est déjà utilisée.';
            header('Location: /admin/users/create');
            exit;
        }
        
        if ($this->userModel->findByUsername($username)) {
            $_SESSION['error'] = 'Ce nom d\'utilisateur est déjà utilisé.';
            header('Location: /admin/users/create');
            exit;
        }
        
        // Création de l'utilisateur
        $userId = $this->userModel->create([
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'role_id' => $roleId,
            'status' => $status
        ]);
        
        if ($userId) {
            $_SESSION['success'] = 'Utilisateur créé avec succès.';
            header('Location: /admin/users');
            exit;
        } else {
            $_SESSION['error'] = 'Une erreur est survenue lors de la création de l\'utilisateur.';
            header('Location: /admin/users/create');
            exit;
        }
    }
    
    public function showEditUserForm($id) {
        $user = $this->userModel->findById($id);
        
        if (!$user) {
            $_SESSION['error'] = 'Utilisateur non trouvé.';
            header('Location: /admin/users');
            exit;
        }
        
        // Récupération des rôles disponibles
        $db = Database::getInstance();
        $roles = $db->fetchAll("SELECT * FROM roles");
        
        require_once __DIR__ . '/../views/admin/edit_user.php';
    }
    
    public function updateUser($id) {
        // Vérification CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['error'] = 'Erreur de sécurité. Veuillez réessayer.';
            header('Location: /admin/users/edit/' . $id);
            exit;
        }
        
        $user = $this->userModel->findById($id);
        
        if (!$user) {
            $_SESSION['error'] = 'Utilisateur non trouvé.';
            header('Location: /admin/users');
            exit;
        }
        
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? ''; // Optionnel
        $roleId = $_POST['role_id'] ?? '';
        $status = $_POST['status'] ?? 'active';
        
        // Validation des champs obligatoires
        if (empty($username) || empty($email) || empty($roleId)) {
            $_SESSION['error'] = 'Les champs nom d\'utilisateur, email et rôle sont obligatoires.';
            header('Location: /admin/users/edit/' . $id);
            exit;
        }
        
        // Validation de l'email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Adresse email invalide.';
            header('Location: /admin/users/edit/' . $id);
            exit;
        }
        
        // Vérification si l'email ou le nom d'utilisateur existe déjà (sauf pour l'utilisateur actuel)
        $existingEmail = $this->userModel->findByEmail($email);
        if ($existingEmail && $existingEmail['id'] != $id) {
            $_SESSION['error'] = 'Cette adresse email est déjà utilisée.';
            header('Location: /admin/users/edit/' . $id);
            exit;
        }
        
        $existingUsername = $this->userModel->findByUsername($username);
        if ($existingUsername && $existingUsername['id'] != $id) {
            $_SESSION['error'] = 'Ce nom d\'utilisateur est déjà utilisé.';
            header('Location: /admin/users/edit/' . $id);
            exit;
        }
        
        // Préparation des données à mettre à jour
        $userData = [
            'username' => $username,
            'email' => $email,
            'role_id' => $roleId,
            'status' => $status
        ];
        
        // Ajout du mot de passe seulement s'il est fourni
        if (!empty($password)) {
            $userData['password'] = $password;
        }
        
        // Mise à jour de l'utilisateur
        $this->userModel->update($id, $userData);
        
        $_SESSION['success'] = 'Utilisateur mis à jour avec succès.';
        header('Location: /admin/users');
        exit;
    }
    
    public function deleteUser($id) {
        $user = $this->userModel->findById($id);
        
        if (!$user) {
            $_SESSION['error'] = 'Utilisateur non trouvé.';
            header('Location: /admin/users');
            exit;
        }
        
        // Éviter la suppression de son propre compte
        if ($id == $_SESSION['user_id']) {
            $_SESSION['error'] = 'Vous ne pouvez pas supprimer votre propre compte.';
            header('Location: /admin/users');
            exit;
        }
        
        // Suppression de l'utilisateur
        $this->userModel->delete($id);
        
        $_SESSION['success'] = 'Utilisateur supprimé avec succès.';
        header('Location: /admin/users');
        exit;
    }
    
    public function toggleUserStatus($id) {
        $user = $this->userModel->findById($id);
        
        if (!$user) {
            $_SESSION['error'] = 'Utilisateur non trouvé.';
            header('Location: /admin/users');
            exit;
        }
        
        // Éviter la désactivation de son propre compte
        if ($id == $_SESSION['user_id']) {
            $_SESSION['error'] = 'Vous ne pouvez pas modifier le statut de votre propre compte.';
            header('Location: /admin/users');
            exit;
        }
        
        // Basculement du statut
        $newStatus = $user['status'] === 'active' ? 'inactive' : 'active';
        $this->userModel->updateStatus($id, $newStatus);
        
        // Fermer toutes les sessions actives si désactivation
        if ($newStatus === 'inactive') {
            $this->sessionModel->closeAllUserSessions($id);
        }
        
        $_SESSION['success'] = 'Statut de l\'utilisateur modifié avec succès.';
        header('Location: /admin/users');
        exit;
    }
}