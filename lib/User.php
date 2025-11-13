<?php
require_once 'lib/DB.php';

class User {
    private $db;

    public function __construct() {
        $this->db = DB::getInstance();
    }

    public static function generateToken($length = 32) {
        return bin2hex(random_bytes($length)); 
    }

    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT);
    }
    
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    public function findByEmail($email) {
        $sql = "SELECT * FROM usuarios WHERE email = ?";
        return $this->db->execute($sql, [$email])->fetch();
    }

    public function findByToken($token) {
        $sql = "SELECT * FROM usuarios WHERE token = ?";
        return $this->db->execute($sql, [$token])->fetch();
    }

    public function findByTokenAction($token_action, $checkRecoveryOrBlocked = false, $checkActive = false) {
        $sql = "SELECT * FROM usuarios WHERE token_action = ?";
        $params = [$token_action];

        if ($checkRecoveryOrBlocked) {
            $sql .= " AND (recupero = 1 OR bloqueado = 1)";
        } elseif ($checkActive) {
            $sql .= " AND activo = 0";
        }
        
        return $this->db->execute($sql, $params)->fetch();
    }

    public function registerUser($email, $nombres, $password) {
        $token = self::generateToken();
        $token_action = self::generateToken();
        $hashed_password = self::hashPassword($password);
        
        $sql = "INSERT INTO usuarios (token, email, nombres, contraseña, activo, bloqueado, recupero, token_action, add_date) 
                VALUES (?, ?, ?, ?, 0, 0, 0, ?, NOW())";
        
        return $this->db->execute($sql, [$token, $email, $nombres, $hashed_password, $token_action])->rowCount() > 0;
    }

    public function activateUser($userId) {
        $sql = "UPDATE usuarios SET activo = 1, token_action = NULL, active_date = NOW(), update_date = NOW() WHERE id = ? AND activo = 0";
        return $this->db->execute($sql, [$userId])->rowCount() > 0;
    }
    
    public function blockUser($userId, $newTokenAction) {
        $sql = "UPDATE usuarios SET bloqueado = 1, activo = 0, recupero = 0, token_action = ?, blocked_date = NOW(), update_date = NOW() WHERE id = ?";
        return $this->db->execute($sql, [$newTokenAction, $userId])->rowCount() > 0;
    }

    public function startRecovery($userId, $newTokenAction) {
        $sql = "UPDATE usuarios SET recupero = 1, token_action = ?, recover_date = NOW(), update_date = NOW() WHERE id = ?";
        return $this->db->execute($sql, [$newTokenAction, $userId])->rowCount() > 0;
    }

    public function resetPassword($userId, $newPassword) {
        $hashed_password = self::hashPassword($newPassword);
        $sql = "UPDATE usuarios SET contraseña = ?, token_action = NULL, activo = 1, recupero = 0, bloqueado = 0, update_date = NOW() WHERE id = ?";
        return $this->db->execute($sql, [$hashed_password, $userId])->rowCount() > 0;
    }
}