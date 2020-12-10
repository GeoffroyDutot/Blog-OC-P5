<?php


namespace App\DTO;


use DateTime;

class CommentDTO extends DTO
{
    protected ?int $id = null;
    protected string $content;
    protected int $id_post;
    protected int $id_user;
    private UserDTO $user;
    protected ?string $status = null;
    protected DateTime $created_at;

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

    public function setId(?int $id): CommentDTO
    {
        $this->id = $id;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): CommentDTO
    {
        $this->content = $content;
        
        return $this;
    }

    public function getIdPost(): int
    {
        return $this->id_post;
    }

    public function setIdPost(int $id_post): CommentDTO
    {
        $this->id_post = $id_post;

        return $this;
    }

    public function getIdUser(): int
    {
        return $this->id_user;
    }

    public function setIdUser(int $id_user): CommentDTO
    {
        $this->id_user = $id_user;

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUserDTO($user)
    {
        $this->user = $user;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): CommentDTO
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        if (!$this->created_at instanceof DateTime)
        {
            $this->created_at = new DateTime($this->created_at);
        }

        return $this->created_at;
    }

    public function setCreatedAt(string $created_at): CommentDTO
    {
        if (!$created_at instanceof DateTime) {
            $created_at = new DateTime($created_at);
        }
        $this->created_at = $created_at;

        return $this;
    }
}