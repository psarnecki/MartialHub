<?php


class Event {
    private $id;
    private $title;
    private $discipline;
    private $description;
    private $date;
    private $location;
    private $country;
    private $registrationFee;
    private $registrationDeadline;
    private $imageUrl;
    private $isFeatured;

    public function __construct(
        string $title,
        string $discipline,
        string $description,
        string $date,
        string $location,
        string $country,
        int $registrationFee,
        string $registrationDeadline,
        string $imageUrl,
        int $id = null,
        bool $isFeatured = false
    ) {
        $this->title = $title;
        $this->discipline = $discipline;
        $this->description = $description;
        $this->date = $date;
        $this->location = $location;
        $this->country = $country;
        $this->registrationFee = $registrationFee;
        $this->registrationDeadline = $registrationDeadline;
        $this->imageUrl = $imageUrl;
        $this->id = $id;
        $this->isFeatured = $isFeatured;
    }

    public function getTitle(): string { 
        return $this->title; 
    }

    public function getDiscipline(): string {
        return $this->discipline;
    }

    public function getDescription(): string { 
        return $this->description; 
    }

    public function getDate(): string { 
        return $this->date; 
    }

    public function getFormattedDate(): string {
        $date = new DateTime($this->date);
        return $date->format('Y-m-d H:i');
    }

    public function getFormattedDay(): string {
        $date = new DateTime($this->date);
        return $date->format('d.m.Y'); 
    }

    public function getLocation(): string { 
        return $this->location; 
    }

    public function getCountry(): string {
        return $this->country;
    }

    public function getRegistrationFee(): int {
        return $this->registrationFee;
    }

    public function getRegistrationDeadline(): string {
        $date = new DateTime($this->registrationDeadline);
        return $date->format('l, j F Y');
    }

    public function getImageUrl(): string { 
        return $this->imageUrl; 
    }

    public function getId(): ?int { 
        return $this->id; 
    }

    public function isFeatured(): bool {
        return $this->isFeatured;
    }

    public function getDaysToRegistrationEnd(): string {
        $deadline = new DateTime($this->registrationDeadline);
        $now = new DateTime();

        if ($now > $deadline) return "Closed";
        
        $diff = $now->diff($deadline);

        return $diff->days === 0 ? "Ends today" : "Ends in " . $diff->days . " days";
    }
}