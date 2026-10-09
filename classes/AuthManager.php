<?php
class AuthManager {
    private PDO $db;
    public function __construct() { $this->db = Database::get(); }

    public function register(string $user, string $email, string $pass, string $confirmPass): array {
        if ($pass !== $confirmPass) throw new Exception('Passwords do not match.');
        $err = [];
        if (!preg_match('/^[A-Za-z0-9_]{3,30}$/', $user)) $err[] = 'Username must be 3-30 letters, numbers or underscores.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $err[] = 'Invalid email.';
        if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d).{6,}$/', $pass)) $err[] = 'Password needs 6+ characters with a letter and a number.';
        if ($err) return $err;
        $s = $this->db->prepare('SELECT 1 FROM users WHERE username=? OR email=?');
        $s->execute([$user, $email]);
        if ($s->fetch()) return ['Username or email already taken.'];
        $this->db->prepare('INSERT INTO users(username,email,password_hash) VALUES(?,?,?)')
            ->execute([$user, $email, password_hash($pass, PASSWORD_DEFAULT)]);
        return [];
    }
    public function login(string $user, string $pass): bool {
        $s = $this->db->prepare('SELECT * FROM users WHERE username=?');
        $s->execute([$user]);
        $u = $s->fetch();
        if ($u && password_verify($pass, $u['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$u['id'];
            $_SESSION['username'] = $u['username'];
            return true;
        }
        return false;
    }
    public function logout(): void { $_SESSION = []; session_destroy(); }
}
