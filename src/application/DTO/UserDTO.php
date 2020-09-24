<?php


namespace App\DTO;


class UserDTO extends DTO {
    protected int $id;
    protected string $email;
    protected string $password;
    protected string $pseudo;
    protected string $role;
    protected ?string $profil_picture = null;

    public function __construct($data) {
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
}