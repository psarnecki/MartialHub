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