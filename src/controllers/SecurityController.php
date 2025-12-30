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
            return $this->render('login');
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = $this->userRepository->getUserByEmail($email);

        if (!$user) {
            return $this->render('login', ['messages' => ['User not found!']]);
        }

        if (!password_verify($password, $user->getPassword())) {
            return $this->render('login', ['messages' => ['Wrong password!']]);
        }

        // TODO create user session, cookie, token

        $url = "http://$_SERVER[HTTP_HOST]";
        header("Location: {$url}/index");
    }

     public function register() {
        if ($this->isGet()) {
            return $this->render('register');
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        $firstName = $_POST['firstName'] ?? '';
        $lastName = $_POST['lastName'] ?? '';

        if (empty($email) || empty($password) || empty($firstName) || empty($lastName)) {
            return $this->render('register', ['messages' => ['Please fill all fields!']]);
        }

                if ($password !== $passwordConfirm) {
            return $this->render('register', ['messages' => ['Passwords should be the same!']]);
        }

        if ($this->userRepository->getUserByEmail($email)) {
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
            return $this->render('login', ['messages' => ['Registration successful! Please log in.']]);
        } catch (Exception $e) {
            return $this->render('register', ['messages' => ['Database error, please try again.']]);
        }
    }
}