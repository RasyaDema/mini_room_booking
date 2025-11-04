<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../middleware/csrf.php';

class AuthController
{
    public function login($data)
    {
        // CSRF
        if (!validate_csrf($data['_csrf'] ?? '')) {
            $_SESSION['flash']['error'] = 'Invalid CSRF token.';
            header('Location: ?action=login');
            return;
        }

        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';
        $user = User::findByEmail($email);
        if (!$user || !password_verify($password, $user['password'])) {
            $_SESSION['flash']['error'] = 'Email or password incorrect.';
            header('Location: ?action=login');
            return;
        }
        // success
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role_id'] = $user['role_id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['flash']['success'] = 'Welcome back, ' . htmlspecialchars($user['name']);
        header('Location: ?action=dashboard');
    }

    public function logout()
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        header('Location: ?');
    }
}
