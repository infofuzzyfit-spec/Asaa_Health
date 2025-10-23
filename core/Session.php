<?php
/**
 * Session Class
 * Session management and security
 */

class Session {
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public function set($key, $value) {
        $_SESSION[$key] = $value;
        return $this;
    }
    
    public function get($key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }
    
    public function has($key) {
        return isset($_SESSION[$key]);
    }
    
    public function remove($key) {
        unset($_SESSION[$key]);
        return $this;
    }
    
    public function destroy() {
        session_destroy();
        return $this;
    }
    
    public function regenerate() {
        session_regenerate_id(true);
        return $this;
    }
    
    public function flash($key, $value = null) {
        if ($value === null) {
            $value = $this->get($key);
            $this->remove($key);
            return $value;
        }
        
        $this->set($key, $value);
        return $this;
    }
    
    public function all() {
        return $_SESSION;
    }
}
