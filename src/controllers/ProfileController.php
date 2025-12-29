<?php

require_once 'AppController.php';

class ProfileController extends AppController {

    public function profile() {
        return $this->render('profile');
    }
}