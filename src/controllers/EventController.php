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

            header('Content-Type: application/json');
            http_response_code(200);

            $events = $this->eventRepository->getEventsWithFilters($decoded);
            
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
        $disciplines = $this->eventRepository->getUniqueDisciplines();
        $locations = $this->eventRepository->getUniqueLocations();

        return $this->render('events', [
            'events' => $events,
            'disciplines' => $disciplines,
            'locations' => $locations
        ]);
    }

    public function eventDetails(int $id) {
        $event = $this->eventRepository->getEventById($id);

        if (!$event) {
            return $this->render('404');
        }

        return $this->render('event-details', ['event' => $event]);
    }

    public function eventResults(int $id) {
        header('Content-Type: application/json');
        $results = $this->eventRepository->getEventResults($id);
        
        echo json_encode($results);
        exit;
    }
}