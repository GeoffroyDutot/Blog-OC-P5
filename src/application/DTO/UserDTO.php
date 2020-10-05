<?php


namespace App\DTO;


use DateTime;

class UserDTO extends DTO {
    protected int $id;
    protected string $email;
    protected string $password;
    protected string $pseudo;
    protected string $role;
    protected ?string $profil_picture = null;
    protected DateTime $date_registered;

    public function __construct($data = null) {
        if ($data) {
            $this->hydrate($data);
        }
    }

    public function getId(): int {
        return $this->id;
    }

    public function setId(int $id): UserDTO {
        $this->id = $id;
        return $this;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function setEmail(string $email): UserDTO {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): string {
        return $this->password;
    }

    public function setPassword(string $password): UserDTO {
        $this->password = $password;
        return $this;
    }

    public function getPseudo(): string {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): UserDTO {
        $this->pseudo = $pseudo;
        return $this;
    }

    public function getRole(): string {
        return $this->role;
    }

    public function setRole(string $role): UserDTO {
        $this->role = $role;
        return $this;
    }

    public function getProfilPicture(): ?string {
        return $this->profil_picture;
    }

    public function setProfilPicture(?string $profil_picture): UserDTO {
        $this->profil_picture = $profil_picture;
        return $this;
    }

    public function getDateRegistered(): DateTime
    {
        if (!$this->date_registered instanceof DateTime) {
            $this->date_registered = new DateTime($this->date_registered);
        }

        return $this->date_registered;
    }

    public function setDateRegistered(string $date_registered): UserDTO {
        if (!$date_registered instanceof DateTime) {
            $date_registered = new DateTime($date_registered);
        }

        $this->date_registered = $date_registered;

        return $this;
    }
}