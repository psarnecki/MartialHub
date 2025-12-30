<?php

require_once 'AppController.php';
require_once __DIR__.'/../repository/EventRepository.php';

class EventController extends AppController {

    private $eventRepository;

    public function __construct() {
        $this->eventRepository = new EventRepository();
    }

    public function index() {
        $events = $this->eventRepository->getEvents();
        return $this->render('index', ['events' => $events]);
    }

    public function events() {
        $events = $this->eventRepository->getEvents();
        return $this->render('events', ['events' => $events]);
    }
}