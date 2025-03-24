<?php
// app/models/Session.php

class Session {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($userId) {
        return $this->db->insert('sessions', [
            'user_id' => $userId
        ]);
    }
    
    public function closeSession($id) {
        $this->db->update(
            'sessions', 
            ['logout_time' => date('Y-m-d H:i:s')], 
            'id = :id AND logout_time IS NULL', 
            ['id' => $id]
        );
    }
    
    public function closeAllUserSessions($userId) {
        $this->db->update(
            'sessions', 
            ['logout_time' => date('Y-m-d H:i:s')], 
            'user_id = :user_id AND logout_time IS NULL', 
            ['user_id' => $userId]
        );
    }
    
    public function getCurrentSession() {
        if (isset($_SESSION['session_id'])) {
            return $this->db->fetch(
                "SELECT * FROM sessions WHERE id = :id", 
                ['id' => $_SESSION['session_id']]
            );
        }
        return false;
    }
    
    public function getUserSessions($userId) {
        return $this->db->fetchAll(
            "SELECT * FROM sessions WHERE user_id = :user_id ORDER BY login_time DESC", 
            ['user_id' => $userId]
        );
    }
    
    public function getAllActiveSessions() {
        return $this->db->fetchAll(
            "SELECT s.*, u.username, u.email 
             FROM sessions s 
             JOIN users u ON s.user_id = u.id 
             WHERE s.logout_time IS NULL 
             ORDER BY s.login_time DESC"
        );
    }
    
    public function getAllSessions($limit = 100) {
        return $this->db->fetchAll(
            "SELECT s.*, u.username, u.email 
             FROM sessions s 
             JOIN users u ON s.user_id = u.id 
             ORDER BY s.login_time DESC 
             LIMIT :limit",
            ['limit' => $limit]
        );
    }
}