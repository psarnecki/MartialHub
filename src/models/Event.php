<?php


class Event {
    private $id;
    private $title;
    private $description;
    private $date;
    private $location;
    private $imageUrl;

    public function __construct(
        string $title,
        string $description,
        string $date,
        string $location,
        string $imageUrl,
        int $id = null
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->date = $date;
        $this->location = $location;
        $this->imageUrl = $imageUrl;
        $this->id = $id;
    }

    public function getTitle(): string { 
        return $this->title; 
    }

    public function getDescription(): string { 
        return $this->description; 
    }

    public function getDate(): string { 
        return $this->date; 
    }

    public function getLocation(): string { 
        return $this->location; 
    }

    public function getImageUrl(): string { 
        return $this->imageUrl; 
    }
    
    public function getId(): ?int { 
        return $this->id; 
    }
}