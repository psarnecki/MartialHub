<?php

require_once 'Repository.php';
require_once __DIR__.'/../models/Event.php';

class EventRepository extends Repository {

    public function getEvents(): array {
        $result = [];

        $query = $this->database->connect()->prepare('
            SELECT * FROM events ORDER BY date ASC
        ');
        $query->execute();
        $events = $query->fetchAll(PDO::FETCH_ASSOC);

        foreach ($events as $event) {
            $result[] = new Event(
                $event['title'],
                $event['discipline'],
                $event['description'],
                $event['date'],
                $event['location'],
                $event['image_url'],
                $event['id'],
                $event['is_featured']
            );
        }

        return $result;
    }

    public function getFeaturedEvent(): ?Event {
        $query = $this->database->connect()->prepare('
            SELECT * FROM events WHERE is_featured = TRUE LIMIT 1
        ');
        $query->execute();
        $event = $query->fetch(PDO::FETCH_ASSOC);

        if (!$event) return null;

        return new Event(
            $event['title'], 
            $event['discipline'],
            $event['description'], 
            $event['date'],
            $event['location'], 
            $event['image_url'], 
            $event['id'], 
            $event['is_featured']
        );
    }

    public function getEventsByStatus(string $status): array {
        $result = [];
        $now = date('Y-m-d H:i:s');
        
        if ($status === 'UPCOMING') {
            $sql = 'SELECT * FROM events WHERE date >= :now ORDER BY date ASC';
        } else {
            $sql = 'SELECT * FROM events WHERE date < :now ORDER BY date DESC';
        }

        $query = $this->database->connect()->prepare($sql);
        $query->bindParam(':now', $now);
        $query->execute();
        $events = $query->fetchAll(PDO::FETCH_ASSOC);

        foreach ($events as $event) {
            $result[] = new Event(
                $event['title'], 
                $event['discipline'],
                $event['description'], 
                $event['date'],
                $event['location'], 
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

        $query = $this->database->connect()->prepare('
            SELECT * FROM events 
            WHERE LOWER(title) LIKE :search OR LOWER(location) LIKE :search
            ORDER BY date ASC
        ');
        $query->bindParam(':search', $searchString, PDO::PARAM_STR);
        $query->execute();
        $events = $query->fetchAll(PDO::FETCH_ASSOC);

        foreach ($events as $event) {
            $result[] = new Event(
                $event['title'], 
                $event['discipline'],
                $event['description'], 
                $event['date'],
                $event['location'], 
                $event['image_url'], 
                $event['id'], 
                $event['is_featured']
            );
        }

        return $result;
    }

    public function getEventById(int $id): ?Event {
        $query = $this->database->connect()->prepare('
            SELECT * FROM events WHERE id = :id
        ');
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();
        $event = $query->fetch(PDO::FETCH_ASSOC);

        if (!$event) return null;

        return new Event(
            $event['title'], 
            $event['discipline'], 
            $event['description'], 
            $event['date'], 
            $event['location'], 
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
        $sql = 'SELECT * FROM events WHERE 1=1';
        $params = [];

        // Status
        $now = date('Y-m-d H:i:s');
        if (($filters['status'] ?? 'UPCOMING') === 'UPCOMING') {
            $sql .= ' AND date >= :now';
        } else {
            $sql .= ' AND date < :now';
        }
        $params['now'] = $now;

        // Text search
        if (!empty($filters['search'])) {
            $sql .= ' AND (LOWER(title) LIKE :search OR LOWER(location) LIKE :search)';
            $params['search'] = '%' . strtolower($filters['search']) . '%';
        }

        // Discipline 
        if (!empty($filters['discipline']) && $filters['discipline'] !== 'ALL DISCIPLINES') {
            $sql .= ' AND UPPER(discipline) = :discipline';
            $params['discipline'] = $filters['discipline'];
        }

        // Location
        if (!empty($filters['location']) && $filters['location'] !== 'ALL LOCATIONS') {
            $sql .= ' AND UPPER(location) = :location';
            $params['location'] = $filters['location'];
        }

        // Specific date
        if (!empty($filters['date'])) {
            $sql .= ' AND date::date = :selected_date';
            $params['selected_date'] = $filters['date'];
        }

        $sql .= ' ORDER BY date ASC';

        $query = $this->database->connect()->prepare($sql);
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
                $event['date'], 
                $event['location'], 
                $event['image_url'], 
                $event['id'], 
                $event['is_featured']
            );
        }
        
        return $result;
    }
}