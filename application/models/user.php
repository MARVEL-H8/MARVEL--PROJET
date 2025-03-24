<?php
// app/models/User.php

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($userData) {
        // Hashage du mot de passe avec bcrypt
        $userData['password'] = password_hash($userData['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        
        return $this->db->insert('users', $userData);
    }
    
    public function findById($id) {
        return $this->db->fetch("SELECT u.*, r.name as role_name 
                              FROM users u 
                              JOIN roles r ON u.role_id = r.id 
                              WHERE u.id = :id", 
                              ['id' => $id]);
    }
    
    public function findByEmail($email) {
        return $this->db->fetch("SELECT u.*, r.name as role_name 
                              FROM users u 
                              JOIN roles r ON u.role_id = r.id 
                              WHERE u.email = :email", 
                              ['email' => $email]);
    }
    
    public function findByUsername($username) {
        return $this->db->fetch("SELECT u.*, r.name as role_name 
                              FROM users u 
                              JOIN roles r ON u.role_id = r.id 
                              WHERE u.username = :username", 
                              ['username' => $username]);
    }
    
    public function getAll() {
        return $this->db->fetchAll("SELECT u.*, r.name as role_name 
                                 FROM users u 
                                 JOIN roles r ON u.role_id = r.id 
                                 ORDER BY u.created_at DESC");
    }
    
    public function update($id, $userData) {
        // Si le mot de passe est présent, le hasher
        if (isset($userData['password']) && !empty($userData['password'])) {
            $userData['password'] = password_hash($userData['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        } else {
            // Ne pas mettre à jour le mot de passe s'il est vide
            unset($userData['password']);
        }
        
        $this->db->update('users', $userData, 'id = :id', ['id' => $id]);
    }
    
    public function delete($id) {
        $this->db->delete('users', 'id = :id', ['id' => $id]);
    }
    
    public function updateStatus($id, $status) {
        $this->db->update('users', ['status' => $status], 'id = :id', ['id' => $id]);
    }
    
    public function verifyPassword($plainPassword, $hashedPassword) {
        return password_verify($plainPassword, $hashedPassword);
    }
}