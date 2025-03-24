<?php
// public/index.php

// Démarrage de la session
session_start();

// Configuration de base
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Inclure le layout principal
require_once __DIR__ . '/../views/layouts/main.php';

// Chargement des fichiers système
require_once __DIR__ . '/../system/app.php';
require_once __DIR__ . '/../system/database.php';
require_once __DIR__ . '/../system/router.php';

// Chargement des middlewares
require_once __DIR__ . '/../middlewares/authmiddleware.php';
require_once __DIR__ . '/../middlewares/rolemiddleware.php';

// Chargement des modèles
require_once __DIR__ . '/../models/user.php';
require_once __DIR__ . '/../models/role.php';
require_once __DIR__ . '/../models/session.php';

// Chargement des contrôleurs
require_once __DIR__ . '/../controllers/Homecontroller.php';
require_once __DIR__ . '/../controllers/authcontroller.php';
require_once __DIR__ . '/../controllers/usercontroller.php';
require_once __DIR__ . '/../controllers/admincontroller.php';

// Chargement des helpers
require_once __DIR__ . '/../helpers/security.php';

// Création d'un token CSRF si inexistant
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Initialisation du routeur
$router = new Router();

// Routes publiques
$router->get('/', 'HomeController@index');
$router->get('/login', 'AuthController@showLoginForm');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@showRegisterForm');
$router->post('/register', 'AuthController@register');
$router->get('/logout', 'AuthController@logout');

// Routes utilisateur (requiert authentification)
$router->get('/user/profile', 'UserController@profile', [new AuthMiddleware()]);
$router->post('/user/profile', 'UserController@updateProfile', [new AuthMiddleware()]);
$router->get('/user/history', 'UserController@history', [new AuthMiddleware()]);

// Routes admin (requiert rôle admin)
$router->get('/admin/dashboard', 'AdminController@dashboard', [new AuthMiddleware(), new RoleMiddleware('admin')]);
$router->get('/admin/users', 'AdminController@users', [new AuthMiddleware(), new RoleMiddleware('admin')]);
$router->get('/admin/logs', 'AdminController@logs', [new AuthMiddleware(), new RoleMiddleware('admin')]);
$router->get('/admin/users/create', 'AdminController@showCreateUserForm', [new AuthMiddleware(), new RoleMiddleware('admin')]);
$router->post('/admin/users/create', 'AdminController@createUser', [new AuthMiddleware(), new RoleMiddleware('admin')]);
$router->get('/admin/users/edit/{id}', 'AdminController@showEditUserForm', [new AuthMiddleware(), new RoleMiddleware('admin')]);
$router->post('/admin/users/edit/{id}', 'AdminController@updateUser', [new AuthMiddleware(), new RoleMiddleware('admin')]);
$router->get('/admin/users/delete/{id}', 'AdminController@deleteUser', [new AuthMiddleware(), new RoleMiddleware('admin')]);
$router->get('/admin/users/toggle/{id}', 'AdminController@toggleUserStatus', [new AuthMiddleware(), new RoleMiddleware('admin')]);

// Exécution du routeur
$router->resolve();