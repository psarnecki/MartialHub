<?php

require_once __DIR__.'/../models/Event.php';

class EventViewModel {
    private Event $event;

    public function __construct(Event $event) {
        $this->event = $event;
    }

    public function getId(): ?int {
        return $this->event->getId();
    }

    public function getTitle(): string {
        return $this->event->getTitle();
    }

    public function getDiscipline(): string {
        return $this->event->getDiscipline();
    }

    public function getDescription(): string {
        return $this->event->getDescription();
    }

    public function getOrganizerEmail(): string {
        return $this->event->getOrganizerEmail();
    }

    public function getOrganizerPhone(): string {
        return $this->event->getOrganizerPhone();
    }

    public function getDate(): string {
        return $this->event->getDate();
    }

    public function getLocation(): string {
        return $this->event->getLocation();
    }

    public function getCountry(): string {
        return $this->event->getCountry();
    }

    public function getRegistrationFee(): int {
        return $this->event->getRegistrationFee();
    }

    public function getImageUrl(): string {
        return $this->event->getImageUrl();
    }

    public function isFeatured(): bool {
        return $this->event->isFeatured();
    }

    public function getFormattedDate(): string {
        $date = new DateTime($this->event->getDate());
        return $date->format('Y-m-d H:i');
    }

    public function getFormattedDay(): string {
        $date = new DateTime($this->event->getDate());
        return $date->format('d.m.Y');
    }

    public function getFormattedRegistrationDeadline(): string {
        $date = new DateTime($this->event->getRegistrationDeadline());
        return $date->format('l, j F Y');
    }

    public function getDaysToRegistrationEnd(): string {
        $deadline = new DateTime($this->event->getRegistrationDeadline());
        $now = new DateTime();

        if ($now > $deadline) return "Closed";
        
        $diff = $now->diff($deadline);

        return $diff->days === 0 ? "Ends today" : "Ends in " . $diff->days . " days";
    }

    public function isRegistrationOpen(): bool {
        $deadline = $this->event->getRegistrationDeadline();
        if (!$deadline) {
            return false;
        }

        $deadlineDate = new DateTime($deadline);
        $now = new DateTime();
        
        return $now <= $deadlineDate;
    }
}