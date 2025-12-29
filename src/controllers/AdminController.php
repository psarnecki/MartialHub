<?php

require_once 'AppController.php';

class AdminController extends AppController {

    public function users() {
        return $this->render('admin-users');
    }
}