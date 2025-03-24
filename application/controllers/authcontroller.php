<?php
// app/controllers/AuthController.php

class AuthController {
    private $userModel;
    private $sessionModel;
    
    public function __construct() {
        $this->userModel = new User();
        $this->sessionModel = new Session();
    }
    
    public function showLoginForm() {
        // Si déjà connecté, rediriger vers le tableau de bord approprié
        if (isset($_SESSION['user_id'])) {
            $this->redirectBasedOnRole();
            exit;
        }
        
        require_once __DIR__ . '/../views/auth/login.php';
    }
    
    public function login() {
        // Vérification CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['error'] = 'Erreur de sécurité. Veuillez réessayer.';
            header('Location: /login');
            exit;
        }
        
        $identifier = $_POST['identifier'] ?? '';  // Email ou nom d'utilisateur
        $password = $_POST['password'] ?? '';
        
        // Vérification des champs
        if (empty($identifier) || empty($password)) {
            $_SESSION['error'] = 'Tous les champs sont obligatoires.';
            header('Location: /login');
            exit;
        }
        
        // Recherche par email ou nom d'utilisateur
        $user = filter_var($identifier, FILTER_VALIDATE_EMAIL)
            ? $this->userModel->findByEmail($identifier)
            : $this->userModel->findByUsername($identifier);
        
        // Vérification de l'utilisateur et du mot de passe
        if (!$user || !$this->userModel->verifyPassword($password, $user['password'])) {
            // Délai pour prévenir le timing attack
            sleep(1);
            $_SESSION['error'] = 'Identifiants incorrects.';
            header('Location: /login');
            exit;
        }
        
        // Vérification du statut du compte
        if ($user['status'] !== 'active') {
            $_SESSION['error'] = 'Votre compte est désactivé. Veuillez contacter l\'administrateur.';
            header('Location: /login');
            exit;
        }
        
        // Création d'une nouvelle session
        $sessionId = $this->sessionModel->create($user['id']);
        
        // Régénération de l'ID de session pour éviter la fixation de session
        session_regenerate_id(true);
        
        // Enregistrement des informations utilisateur en session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role_name'];
        $_SESSION['session_id'] = $sessionId;
        
        // Redirection vers le tableau de bord approprié
        $this->redirectBasedOnRole();
    }
    
    public function showRegisterForm() {
        // Si déjà connecté, rediriger
        if (isset($_SESSION['user_id'])) {
            $this->redirectBasedOnRole();
            exit;
        }
        
        require_once __DIR__ . '/../views/auth/register.php';
    }
    
    public function register() {
        // Vérification CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['error'] = 'Erreur de sécurité. Veuillez réessayer.';
            header('Location: /register');
            exit;
        }
        
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        
        // Validation des champs
        if (empty($username) || empty($email) || empty($password) || empty($passwordConfirm)) {
            $_SESSION['error'] = 'Tous les champs sont obligatoires.';
            header('Location: /register');
            exit;
        }
        
        // Validation de l'email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Adresse email invalide.';
            header('Location: /register');
            exit;
        }
        
        // Vérification de la correspondance des mots de passe
        if ($password !== $passwordConfirm) {
            $_SESSION['error'] = 'Les mots de passe ne correspondent pas.';
            header('Location: /register');
            exit;
        }
        
        // Vérification de la complexité du mot de passe
        if (strlen($password) < 8 || 
            !preg_match('/[A-Z]/', $password) || 
            !preg_match('/[a-z]/', $password) || 
            !preg_match('/[0-9]/', $password)) {
            $_SESSION['error'] = 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un chiffre.';
            header('Location: /register');
            exit;
        }
        
        // Vérification si l'email ou le nom d'utilisateur existe déjà
        if ($this->userModel->findByEmail($email)) {
            $_SESSION['error'] = 'Cette adresse email est déjà utilisée.';
            header('Location: /register');
            exit;
        }
        
        if ($this->userModel->findByUsername($username)) {
            $_SESSION['error'] = 'Ce nom d\'utilisateur est déjà utilisé.';
            header('Location: /register');
            exit;
        }
        
        // Par défaut, les nouveaux utilisateurs ont le rôle "client"
        $role = $this->getRoleIdByName('client');
        
        // Création de l'utilisateur
        $userId = $this->userModel->create([
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'role_id' => $role,
            'status' => 'active'
        ]);
        
        if ($userId) {
            $_SESSION['success'] = 'Compte créé avec succès. Vous pouvez maintenant vous connecter.';
            header('Location: /login');
            exit;
        } else {
            $_SESSION['error'] = 'Une erreur est survenue lors de la création du compte.';
            header('Location: /register');
            exit;
        }
    }
    
    public function logout() {
        // Fermeture de la session en base de données
        if (isset($_SESSION['session_id'])) {
            $this->sessionModel->closeSession($_SESSION['session_id']);
        }
        
        // Suppression des variables de session
        $_SESSION = [];
        
        // Destruction du cookie de session
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        
        // Destruction de la session
        session_destroy();
        
        // Redirection vers la page de connexion
        header('Location: /login');
        exit;
    }
    
    private function redirectBasedOnRole() {
        if ($_SESSION['role'] === 'admin') {
            header('Location: /admin/dashboard');
        } else {
            header('Location: /user/profile');
        }
        exit;
    }
    
    private function getRoleIdByName($roleName) {
        $db = Database::getInstance();
        $role = $db->fetch("SELECT id FROM roles WHERE name = :name", ['name' => $roleName]);
        return $role['id'] ?? 1; // Retourne l'ID du rôle ou 1 par défaut
    }
}