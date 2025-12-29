<?php

require_once 'AppController.php';

class SecurityController extends AppController {

    public function login() {

        if (!$this->isPost()) {
            return $this->render('login');
        }

        $url = "http://$_SERVER[HTTP_HOST]";
        header("Location: {$url}/index");
    }

    public function register() {
        
        if (!$this->isPost()) {
            return $this->render('register');
        }

        return $this->render('login', ['messages' => ['Registered successfully!']]);
    }
}