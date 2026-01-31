<?php


class Event {
    private $id;
    private $title;
    private $discipline;
    private $description;
    private $organizerEmail;
    private $organizerPhone;
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
        ?string $organizerEmail, 
        ?string $organizerPhone,
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
        $this->organizerEmail = $organizerEmail ?? 'events@martialhub.com';
        $this->organizerPhone = $organizerPhone ?? 'Not provided';
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

    public function getOrganizerEmail(): string {
        return $this->organizerEmail;
    }

    public function getOrganizerPhone(): string {
        return $this->organizerPhone;
    }

    public function getDate(): string { 
        return $this->date; 
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
        return $this->registrationDeadline;
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
}