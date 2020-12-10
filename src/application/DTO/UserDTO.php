<?php


namespace App\DTO;


use DateTime;

class UserDTO extends DTO
{
    protected ?int $id = null;
    protected string $email;
    protected string $password;
    protected string $pseudo;
    protected string $role = 'ROLE_USER';
    protected ?string $profil_picture = null;
    protected DateTime $date_registered;
    protected int $is_deactivated = 0;
    protected ?string $reason_deactivation = null;
    protected ?DateTime $deactivated_at = null;

    public function __construct($data = null)
    {
        if ($data) {
            $this->hydrate($data);
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): UserDTO
    {
        $this->id = $id;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): UserDTO
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): UserDTO
    {
        $this->password = $password;

        return $this;
    }

    public function getPseudo(): string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): UserDTO
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): UserDTO
    {
        $this->role = $role;

        return $this;
    }

    public function getProfilPicture(): ?string
    {
        return $this->profil_picture;
    }

    public function setProfilPicture(?string $profil_picture): UserDTO
    {
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

    public function setDateRegistered(string $date_registered): UserDTO
    {
        if (!$date_registered instanceof DateTime) {
            $date_registered = new DateTime($date_registered);
        }
        $this->date_registered = $date_registered;

        return $this;
    }

    public function getIsDeactivated(): int
    {
        return $this->is_deactivated;
    }

    public function setIsDeactivated(int $is_deactivated): UserDTO
    {
        $this->is_deactivated = $is_deactivated;

        return $this;
    }

    public function getReasonDeactivation(): ?string
    {
        return $this->reason_deactivation;
    }

    public function setReasonDeactivation(?string $reason_deactivation): UserDTO
    {
        $this->reason_deactivation = $reason_deactivation;

        return $this;
    }

    public function getDeactivatedAt(): ?DateTime
    {
        return $this->deactivated_at;
    }

    public function setDeactivatedAt(?string $deactivated_at): UserDTO
    {
        if (!$deactivated_at instanceof DateTime) {
            if (!empty($deactivated_at)) {
                $deactivated_at = new DateTime($deactivated_at);
            } else {
                $deactivated_at = null;
            }
        }
        $this->deactivated_at = $deactivated_at;

        return $this;
    }
}