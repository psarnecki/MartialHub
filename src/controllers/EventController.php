<?php

require_once 'AppController.php';

class EventController extends AppController {

    public function index() {
        return $this->render('index');
    }

    public function events() {
        return $this->render('events');
    }

    public function eventDetails() {
        return $this->render('event-details');
    }
}