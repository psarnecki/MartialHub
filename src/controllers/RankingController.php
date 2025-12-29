<?php

require_once 'AppController.php';

class RankingController extends AppController {

    public function rankings() {
        return $this->render('rankings');
    }
}