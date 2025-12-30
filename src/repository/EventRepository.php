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
                $event['id']
            );
        }

        return $result;
    }
}