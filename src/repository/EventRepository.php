<?php

require_once 'Repository.php';
require_once __DIR__.'/../models/Event.php';
require_once __DIR__.'/../viewmodels/EventViewModel.php';

class EventRepository extends Repository {

    private function mapToEventViewModel(array $data): EventViewModel {
        $event = new Event(
            $data['title'],
            $data['discipline'],
            $data['description'],
            $data['organizer_email'] ?? null,
            $data['organizer_phone'] ?? null,
            $data['date'],
            $data['location'],
            $data['country'],
            $data['registration_fee'],
            $data['registration_deadline'],
            $data['image_url'],
            $data['id'],
            $data['is_featured']
        );
        
        return new EventViewModel($event);
    }

    private function getBaseQuery(): string {
        return '
            SELECT e.*, u.email as organizer_email, ud.phone as organizer_phone
            FROM events e
            LEFT JOIN users u ON e.organizer_id = u.id
            LEFT JOIN user_details ud ON u.id = ud.user_id
        ';
    }

    public function getEvents(): array {
        $result = [];
        $query = $this->database->execute($this->getBaseQuery() . ' ORDER BY date ASC');
        $events = $query->fetchAll(PDO::FETCH_ASSOC);
    
        foreach ($events as $event) {
            $result[] = $this->mapToEventViewModel($event);
        }

        return $result;
    }

    public function getFeaturedEvent(): ?EventViewModel {
        $query = $this->database->execute($this->getBaseQuery() . ' WHERE is_featured = TRUE LIMIT 1');
        $event = $query->fetch(PDO::FETCH_ASSOC);

        return $event ? $this->mapToEventViewModel($event) : null;
    }

    public function getEventsByStatus(string $status): array {
        $result = [];
        $now = date('Y-m-d H:i:s');
        $sql = $this->getBaseQuery();
        
        $sql .= ($status === 'UPCOMING') ? ' WHERE e.date >= :now' : ' WHERE e.date < :now';
        $sql .= ' ORDER BY e.date ASC';

        $query = $this->database->execute($sql, ['now' => $now]);
        $events = $query->fetchAll(PDO::FETCH_ASSOC);

        foreach ($events as $event) {
            $result[] = $this->mapToEventViewModel($event);
        }

        return $result;
    }

    public function getEventsByTitle(string $searchString): array {
        $result = [];
        $searchString = '%' . strtolower($searchString) . '%';

        $query = $this->database->execute(
            $this->getBaseQuery() . ' WHERE LOWER(e.title) LIKE :search OR LOWER(e.location) LIKE :search ORDER BY e.date ASC'
        , ['search' => $searchString]);
        $events = $query->fetchAll(PDO::FETCH_ASSOC);

        foreach ($events as $event) {
            $result[] = $this->mapToEventViewModel($event);
        }

        return $result;
    }

    public function getEventById(int $id): ?EventViewModel {
        $query = $this->database->execute($this->getBaseQuery() . ' WHERE e.id = :id', ['id' => $id]);
        $event = $query->fetch(PDO::FETCH_ASSOC);

        if (!$event) return null;

        return $this->mapToEventViewModel($event);
    }

    public function getUniqueDisciplines(): array {
        $query = $this->database->execute('
            SELECT DISTINCT UPPER(discipline) AS discipline 
            FROM events 
            WHERE discipline IS NOT NULL 
            ORDER BY discipline ASC
        ');

        return $query->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getUniqueLocations(): array {
        $query = $this->database->execute('
            SELECT DISTINCT UPPER(location) AS location 
            FROM events 
            WHERE location IS NOT NULL 
            ORDER BY location ASC
        ');

        return $query->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getEventsWithFilters(array $filters): array {
        $result = [];
        $sql = $this->getBaseQuery() . ' WHERE 1=1';
        $params = [];
        $now = date('Y-m-d H:i:s');

        // Status
        if (($filters['status'] ?? 'UPCOMING') === 'UPCOMING') {
            $sql .= ' AND e.date >= :now';
        } else {
            $sql .= ' AND e.date < :now';
        }
        $params['now'] = $now;

        // Text search
        if (!empty($filters['search'])) {
            $sql .= ' AND (LOWER(e.title) LIKE :search OR LOWER(e.location) LIKE :search)';
            $params['search'] = '%' . strtolower($filters['search']) . '%';
        }

        // Discipline 
        if (!empty($filters['discipline']) && $filters['discipline'] !== 'ALL DISCIPLINES') {
            $sql .= ' AND UPPER(e.discipline) = :discipline';
            $params['discipline'] = strtoupper($filters['discipline']);
        }

        // Location
        if (!empty($filters['location']) && $filters['location'] !== 'ALL LOCATIONS') {
            $sql .= ' AND UPPER(e.location) = :location';
            $params['location'] = strtoupper($filters['location']);
        }

        // Specific date
        if (!empty($filters['date'])) {
            $sql .= ' AND e.date::date = :selected_date';
            $params['selected_date'] = $filters['date'];
        }

        $query = $this->database->execute($sql . ' ORDER BY e.date ASC', $params);
        $eventsData = $query->fetchAll(PDO::FETCH_ASSOC);

        foreach ($eventsData as $event) {
            $result[] = $this->mapToEventViewModel($event);
        }
        
        return $result;
    }

    public function getEventResults(int $eventId): array {
        $query = $this->database->execute('
            SELECT 
                f.result, f.method, f.fight_date,
                ud1.firstname as fighter_firstname, ud1.lastname as fighter_lastname,
                ud2.firstname as opponent_firstname, ud2.lastname as opponent_lastname
            FROM fights f
            JOIN user_details ud1 ON f.user_id = ud1.user_id
            JOIN user_details ud2 ON f.opponent_id = ud2.user_id
            WHERE f.event_id = :id 
            AND (
                f.result = \'WIN\' 
                OR (f.result = \'DRAW\' AND f.user_id < f.opponent_id)
            )
            ORDER BY f.id ASC
        ', ['id' => $eventId]);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function isUserRegistered(int $eventId, int $userId): bool {
        $query = $this->database->execute('
            SELECT 1 FROM event_registrations 
            WHERE event_id = :event_id AND user_id = :user_id
        ', [
            'event_id' => $eventId, 
            'user_id' => $userId
        ]);

        return $query->fetch() !== false;
    }

    public function getRegistrationCount(int $eventId): int {
        $query = $this->database->execute('
            SELECT COUNT(*) FROM event_registrations WHERE event_id = :event_id
        ', ['event_id' => $eventId]);

        return (int)$query->fetchColumn();
    }

    public function getEventCapacity(int $eventId): ?int {
        $query = $this->database->execute('
            SELECT capacity FROM events WHERE id = :id
        ', ['id' => $eventId]);

        $result = $query->fetchColumn();

        if ($result !== false) return (int)$result;

        return null;
    }

    public function registerUser(int $eventId, int $userId): array {
        $db = $this->database->connect();
        
        try {
            $db->beginTransaction();

            $query = $db->prepare('SELECT capacity, registration_deadline FROM events WHERE id = :id FOR UPDATE');
            $query->execute(['id' => $eventId]);
            $event = $query->fetch(PDO::FETCH_ASSOC);

            if (!$event) {
                $db->rollBack();
                return ['success' => false, 'message' => 'Event not found'];
            }

            if (new DateTime($event['registration_deadline']) < new DateTime()) {
                $db->rollBack();
                return ['success' => false, 'message' => 'Registration deadline has passed'];
            }

            $query = $db->prepare('SELECT 1 FROM event_registrations WHERE event_id = :event_id AND user_id = :user_id');
            $query->execute(['event_id' => $eventId, 'user_id' => $userId]);
            if ($query->fetch()) {
                $db->rollBack();
                return ['success' => false, 'message' => 'Already registered'];
            }

            $query = $db->prepare('SELECT COUNT(*) FROM event_registrations WHERE event_id = :event_id');
            $query->execute(['event_id' => $eventId]);
            $currentCount = (int)$query->fetchColumn();
            if ($currentCount >= $event['capacity']) {
                $db->rollBack();
                return ['success' => false, 'message' => 'Event is full'];
            }

            $query = $db->prepare('INSERT INTO event_registrations (user_id, event_id) VALUES (:user_id, :event_id)');
            $query->execute(['user_id' => $userId, 'event_id' => $eventId]);

            $db->commit();
            return ['success' => true, 'message' => 'Registration successful'];
        } catch (Exception $e) {
            $db->rollBack();
            return ['success' => false, 'message' => 'Registration failed'];
        }
    }
}