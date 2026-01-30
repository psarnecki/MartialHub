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
        $filters = $this->getJsonData();

        header('Content-Type: application/json');
        
        $events = $this->eventRepository->getEventsWithFilters($filters);
        
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
        exit;
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
            $this->terminateWithError(404);
        }

        $isLoggedIn = isset($_SESSION['user_id']);
        $isRegistered = false;
        $spotsLeft = null;

        if ($isLoggedIn) {
            $isRegistered = $this->eventRepository->isUserRegistered($id, $_SESSION['user_id']);
        }

        $capacity = $this->eventRepository->getEventCapacity($id);
        $registrationCount = $this->eventRepository->getRegistrationCount($id);
        $spotsLeft = $capacity - $registrationCount;

        return $this->render('event-details', [
            'event' => $event,
            'isLoggedIn' => $isLoggedIn,
            'isRegistered' => $isRegistered,
            'spotsLeft' => $spotsLeft
        ]);
    }

    public function registerEvent(int $id) {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Not logged in', 'redirect' => '/login']);
            exit;
        }

        $result = $this->eventRepository->registerUser($id, $_SESSION['user_id']);

        http_response_code($result['success'] ? 200 : 400);
        echo json_encode($result);
        exit;
    }

    public function eventResults(int $id) {
        header('Content-Type: application/json');
        $results = $this->eventRepository->getEventResults($id);
        
        echo json_encode($results);
        exit;
    }
}