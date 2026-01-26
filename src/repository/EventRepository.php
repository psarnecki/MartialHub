<?php

require_once 'Repository.php';
require_once __DIR__.'/../models/Event.php';

class EventRepository extends Repository {

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
        $query = $this->database->connect()->prepare($this->getBaseQuery() . ' ORDER BY date ASC');
        $query->execute();
        $events = $query->fetchAll(PDO::FETCH_ASSOC);
    
        foreach ($events as $event) {
            $result[] = new Event(
                $event['title'],
                $event['discipline'],
                $event['description'],
                $event['organizer_email'] ?? null,
                $event['organizer_phone'] ?? null,
                $event['date'],
                $event['location'],
                $event['country'],
                $event['registration_fee'],
                $event['registration_deadline'],
                $event['image_url'],
                $event['id'],
                $event['is_featured']
            );
        }

        return $result;
    }

    public function getFeaturedEvent(): ?Event {
        $query = $this->database->connect()->prepare($this->getBaseQuery() . ' WHERE is_featured = TRUE LIMIT 1');
        $query->execute();
        $event = $query->fetch(PDO::FETCH_ASSOC);

        if (!$event) return null;

        return new Event(
            $event['title'],
            $event['discipline'],
            $event['description'],
            $event['organizer_email'] ?? null,
            $event['organizer_phone'] ?? null,
            $event['date'],
            $event['location'],
            $event['country'],
            $event['registration_fee'],
            $event['registration_deadline'],
            $event['image_url'],
            $event['id'],
            $event['is_featured']
        );
    }

    public function getEventsByStatus(string $status): array {
        $result = [];
        $now = date('Y-m-d H:i:s');
        $sql = $this->getBaseQuery();
        
        $sql .= ($status === 'UPCOMING') ? ' WHERE e.date >= :now' : ' WHERE e.date < :now';
        $sql .= ' ORDER BY e.date ASC';

        $query = $this->database->connect()->prepare($sql);
        $query->bindParam(':now', $now);
        $query->execute();
        $events = $query->fetchAll(PDO::FETCH_ASSOC);

        foreach ($events as $event) {
            $result[] = new Event(
                $event['title'],
                $event['discipline'],
                $event['description'],
                $event['organizer_email'] ?? null,
                $event['organizer_phone'] ?? null,
                $event['date'],
                $event['location'],
                $event['country'],
                $event['registration_fee'],
                $event['registration_deadline'],
                $event['image_url'],
                $event['id'],
                $event['is_featured']
            );
        }

        return $result;
    }

    public function getEventsByTitle(string $searchString): array {
        $result = [];
        $searchString = '%' . strtolower($searchString) . '%';

        $query = $this->database->connect()->prepare(
        $this->getBaseQuery() . ' WHERE LOWER(e.title) LIKE :search OR LOWER(e.location) LIKE :search ORDER BY e.date ASC'
        );
        $query->bindParam(':search', $searchString, PDO::PARAM_STR);
        $query->execute();
        $events = $query->fetchAll(PDO::FETCH_ASSOC);

        foreach ($events as $event) {
            $result[] = new Event(
                $event['title'],
                $event['discipline'],
                $event['description'],
                $event['organizer_email'] ?? null,
                $event['organizer_phone'] ?? null,
                $event['date'],
                $event['location'],
                $event['country'],
                $event['registration_fee'],
                $event['registration_deadline'],
                $event['image_url'],
                $event['id'],
                $event['is_featured']
            );
        }

        return $result;
    }

    public function getEventById(int $id): ?Event {
        $query = $this->database->connect()->prepare($this->getBaseQuery() . ' WHERE e.id = :id');
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();
        $event = $query->fetch(PDO::FETCH_ASSOC);

        if (!$event) return null;

        return new Event(
            $event['title'],
            $event['discipline'],
            $event['description'],
            $event['organizer_email'] ?? null,
            $event['organizer_phone'] ?? null,
            $event['date'],
            $event['location'],
            $event['country'],
            $event['registration_fee'],
            $event['registration_deadline'],
            $event['image_url'],
            $event['id'],
            $event['is_featured']
        );
    }

    public function getUniqueDisciplines(): array {
        $query = $this->database->connect()->prepare('
            SELECT DISTINCT UPPER(discipline) AS discipline 
            FROM events 
            WHERE discipline IS NOT NULL 
            ORDER BY discipline ASC
        ');
        $query->execute();
        return $query->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getUniqueLocations(): array {
        $query = $this->database->connect()->prepare('
            SELECT DISTINCT UPPER(location) AS location 
            FROM events 
            WHERE location IS NOT NULL 
            ORDER BY location ASC
        ');
        $query->execute();
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

        $query = $this->database->connect()->prepare($sql . ' ORDER BY e.date ASC');
        foreach ($params as $key => $value) {
            $query->bindValue(':' . $key, $value);
        }
        $query->execute();
        $eventsData = $query->fetchAll(PDO::FETCH_ASSOC);

        foreach ($eventsData as $event) {
            $result[] = new Event(
                $event['title'],
                $event['discipline'],
                $event['description'],
                $event['organizer_email'] ?? null,
                $event['organizer_phone'] ?? null,
                $event['date'],
                $event['location'],
                $event['country'],
                $event['registration_fee'],
                $event['registration_deadline'],
                $event['image_url'],
                $event['id'],
                $event['is_featured']
            );
        }
        
        return $result;
    }

    public function getEventResults(int $eventId): array {
        $query = $this->database->connect()->prepare('
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
        ');
        $query->bindParam(':id', $eventId, PDO::PARAM_INT);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}