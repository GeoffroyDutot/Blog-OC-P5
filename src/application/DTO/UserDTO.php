<?php


namespace App\DTO;


class UserDTO extends DTO {
    protected int $id;
    protected string $email;
    protected string $password;
    protected string $pseudo;
    protected string $role;

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
}