<?php
// system/App.php

class App {
    private static $instance = null;
    private $config;
    
    private function __construct() {
        $this->config = require_once __DIR__ . '/../config/app.php';
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConfig($key = null) {
        if ($key === null) {
            return $this->config;
        }
        
        return $this->config[$key] ?? null;
    }
    
    public function setConfig($key, $value) {
        $this->config[$key] = $value;
    }
}