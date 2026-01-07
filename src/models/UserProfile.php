<?php


class UserProfile {
    private $firstName;
    private $lastName;
    private $role;
    private $clubName;
    private $wins;
    private $losses;
    private $draws;
    private $bio;
    private $imageUrl;

    public function __construct($firstName, $lastName, $role, $clubName, $wins, $losses, $draws, $bio, $imageUrl) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->role = $role;
        $this->clubName = $clubName;
        $this->wins = $wins;
        $this->losses = $losses;
        $this->draws = $draws;
        $this->bio = $bio;
        $this->imageUrl = $imageUrl;
    }

    public function getFullName(): string {
        return $this->firstName . ' ' . $this->lastName;
    }

    public function getRole(): string {
        return $this->role;
    }

    public function getClubName(): string {
        if (!empty($this->clubName)) {
            return $this->clubName;
        }
        return match ($this->role) {
            'admin'     => 'ADMINISTRATOR',
            'organizer' => 'OFFICIAL ORGANIZER',
            default     => 'INDEPENDENT',
        };
    }

    public function getRecord(): string {
        return "{$this->wins} - {$this->losses} - {$this->draws}";
    }

    public function getWins(): int {
        return $this->wins;
    }

    public function getLosses(): int {
        return $this->losses;
    }

    public function getDraws(): int {
        return $this->draws;
    }

    public function getBio(): string {
        return $this->bio ?? '';
    }

    public function getImageUrl(): string {
        return $this->imageUrl;
    }
}