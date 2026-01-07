<?php


class User {
    private $email;
    private $password;
    private $firstName;
    private $lastName;
    private $role;
    private $id;

    public function __construct(
        string $email,
        string $password,
        string $firstName,
        string $lastName,
        string $role = 'user',
        ?int $id = null
    ) {
        $this->email = $email;
        $this->password = $password;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->role = $role;
        $this->id = $id;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getPassword(): string {
        return $this->password;
    }

    public function getFirstName(): string {
        return $this->firstName;
    }

    public function getLastName(): string {
        return $this->lastName;
    }

    public function getRole(): string {
        return $this->role;
    }

    public function setPassword(string $password) {
        $this->password = $password;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function setId(int $id) {
        $this->id = $id;
    }
}