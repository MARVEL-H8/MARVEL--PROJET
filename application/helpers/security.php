<?php
// app/helpers/Security.php

class Security {
    /**
     * Génère un token CSRF
     */
    public static function generateCsrfToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Vérifie un token CSRF
     */
    public static function verifyCsrfToken($token) {
        if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
            return false;
        }
        return true;
    }
    
    /**
     * Échappe les données HTML
     */
    public static function escape($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = self::escape($value);
            }
            return $data;
        }
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Génère un mot de passe aléatoire
     */
    public static function generateRandomPassword($length = 12) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_=+';
        $password = '';
        
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }
        
        return $password;
    }
    
    /**
     * Vérifie la complexité d'un mot de passe
     */
    public static function isPasswordStrong($password) {
        // Au moins 8 caractères
        if (strlen($password) < 8) {
            return false;
        }
        
        // Au moins une lettre majuscule
        if (!preg_match('/[A-Z]/', $password)) {
            return false;
        }
        
        // Au moins une lettre minuscule
        if (!preg_match('/[a-z]/', $password)) {
            return false;
        }
        
        // Au moins un chiffre
        if (!preg_match('/[0-9]/', $password)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Nettoie les données d'entrée
     */
    public static function sanitizeInput($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = self::sanitizeInput($value);
            }
            return $data;
        }
        
        // Supprime les espaces en début et fin de chaîne
        $data = trim($data);
        
        // Supprime les balises HTML et PHP
        $data = strip_tags($data);
        
        return $data;
    }
}