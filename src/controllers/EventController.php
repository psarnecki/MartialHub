<?php

require_once 'AppController.php';
require_once __DIR__.'/../repository/EventRepository.php';

class EventController extends AppController {

    private $eventRepository;

    public function __construct() {
        $this->eventRepository = new EventRepository();
    }

    public function index() {
        $events = $this->eventRepository->getEventsByStatus('UPCOMING');
        $featuredEvent = $this->eventRepository->getFeaturedEvent();

        return $this->render('index', [
            'events' => $events,
            'featuredEvent' => $featuredEvent
        ]);
    }

    public function filter() {
        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

        if (strpos($contentType, "application/json") !== false) {
            $content = trim(file_get_contents("php://input"));
            $decoded = json_decode($content, true);
            $status = $decoded['status'] ?? 'UPCOMING';

            header('Content-Type: application/json');
            http_response_code(200);

            $events = $this->eventRepository->getEventsByStatus($status);
            
            $response = [];
            foreach ($events as $event) {
                $response[] = [
                    'id' => $event->getId(),
                    'title' => $event->getTitle(),
                    'discipline' => $event->getDiscipline(),
                    'day' => $event->getFormattedDay(),
                    'date' => $event->getFormattedDate(), 
                    'location' => $event->getLocation(),
                    'imageUrl' => $event->getImageUrl()
                ];
            }

            echo json_encode($response);
            return;
        }
    }

    public function events() {
        $events = $this->eventRepository->getEventsByStatus('UPCOMING');
        return $this->render('events', ['events' => $events]);
    }
}