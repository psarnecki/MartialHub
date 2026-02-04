<?php

require_once 'AppController.php';
require_once __DIR__.'/../models/User.php';
require_once __DIR__.'/../repository/UserRepository.php';

class SecurityController extends AppController {

    private $userRepository;

    public function __construct() {
        $this->userRepository = new UserRepository();
    }

    public function login() {
        if (!$this->isPost()) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return $this->render('login');
        }

        $token = $_POST['csrf_token'] ?? '';
        if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
            http_response_code(403);
            die(file_get_contents(__DIR__ . '/../../public/views/403.html'));
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (strlen($email) > 255 || strlen($password) > 128) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return $this->render('login', ['messages' => ['Invalid input length!']]);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return $this->render('login', ['messages' => ['Invalid email format.']]);
        }

        $user = $this->userRepository->getUserByEmail($email);

        if (!$user) {
            $emailHash = hash('sha256', $email);
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            error_log("Failed login attempt - User not found. Email hash: {$emailHash}, IP: {$ip}");
            
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return $this->render('login', ['messages' => ['Invalid email or password.']]);
        }

        if (!password_verify($password, $user->getPassword())) {
            $emailHash = hash('sha256', $email);
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            error_log("Failed login attempt - Wrong password. Email hash: {$emailHash}, IP: {$ip}");
            
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return $this->render('login', ['messages' => ['Invalid email or password.']]);
        }

        unset($_SESSION['csrf_token']);
        session_regenerate_id(true);
        
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['user_role'] = $user->getRole();

        $url = "http://$_SERVER[HTTP_HOST]";
        header("Location: {$url}/index");

        exit;
    }

    public function logout() {
        session_destroy();

        $url = "http://$_SERVER[HTTP_HOST]";
        header("Location: {$url}/login");

        exit;
    }

    public function register() {
        if ($this->isGet()) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return $this->render('register');
        }

        $token = $_POST['csrf_token'] ?? '';
        if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
            http_response_code(403);
            die(file_get_contents(__DIR__ . '/../../public/views/403.html'));
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        $firstName = $_POST['firstName'] ?? '';
        $lastName = $_POST['lastName'] ?? '';

        if (empty($email) || empty($password) || empty($firstName) || empty($lastName)) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return $this->render('register', ['messages' => ['Please fill all fields!']]);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return $this->render('register', ['messages' => ['Invalid email format.']]);
        }

        if (strlen($email) > 255 || strlen($password) > 128 || strlen($firstName) > 100 || strlen($lastName) > 100) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return $this->render('register', ['messages' => ['Input is too long!']]);
        }

        if ($password !== $passwordConfirm) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return $this->render('register', ['messages' => ['Passwords should be the same!']]);
        }

        if (strlen($password) < 8) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return $this->render('register', ['messages' => ['Password must be at least 8 characters long!']]);
        }

        if (!preg_match('/[A-Z]/', $password)) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return $this->render('register', ['messages' => ['Password must contain at least one uppercase letter!']]);
        }

        if (!preg_match('/[0-9]/', $password)) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return $this->render('register', ['messages' => ['Password must contain at least one number!']]);
        }

        if ($this->userRepository->getUserByEmail($email)) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return $this->render('register', ['messages' => ['User with this email already exists!']]);
        }

        $user = new User(
            $email, 
            password_hash($password, PASSWORD_BCRYPT), 
            $firstName, 
            $lastName
        );

        try {
            $this->userRepository->addUser($user);
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return $this->render('login', ['messages' => ['Registration successful! Please log in.']]);
        } catch (Exception $e) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return $this->render('register', ['messages' => ['Database error, please try again.']]);
        }
    }
}