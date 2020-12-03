<?php


namespace App\DTO;


use DateTime;

class PostDTO extends DTO
{
    protected ?int $id = null;
    protected string $title;
    protected string $slug;
    protected ?string $subtitle = null;
    protected DateTime $created_at;
    protected ?DateTime $updated_at = null;
    protected string $content;
    protected ?string $resume = null;
    protected ?string $picture = null;
    protected ?DateTime $archived_at = null;
    protected int $is_archived = 0;
    private array $comments = [];

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

    public function setId(?int $id): PostDTO
    {
        $this->id = $id;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): PostDTO
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): PostDTO
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function setSubtitle(?string $subtitle): PostDTO
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        if (!$this->created_at instanceof DateTime) {
            $this->created_at = new DateTime($this->created_at);
        }

        return $this->created_at;
    }

    public function setCreatedAt(string $created_at): PostDTO
    {
        if (!$created_at instanceof DateTime) {
            $created_at = new DateTime($created_at);
        }
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?string $updated_at): PostDTO
    {
        if (!$updated_at instanceof DateTime) {
            if (!empty($updated_at)) {
                $updated_at = new DateTime($updated_at);
            } else {
                $updated_at = null;
            }
        }
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): PostDTO
    {
        $this->content = $content;

        return $this;
    }

    public function getResume(): ?string
    {
        return $this->resume;
    }

    public function setResume(?string $resume): PostDTO
    {
        $this->resume = $resume;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): PostDTO
    {
        $this->picture = $picture;

        return $this;
    }

    public function getArchivedAt(): ?DateTime
    {
        return $this->archived_at;
    }

    public function setArchivedAt(?string $archived_at): PostDTO
    {
        if (!$archived_at instanceof DateTime) {
            if (!empty($archived_at)) {
                $archived_at = new DateTime($archived_at);
            } else {
                $archived_at = null;
            }
        }
        $this->archived_at = $archived_at;

        return $this;
    }

    public function getIsArchived(): int
    {
        return $this->is_archived;
    }

    public function setIsArchived(int $is_archived): PostDTO
    {
        $this->is_archived = $is_archived;

        return $this;
    }

    public function getComments(): array
    {
        return $this->comments;
    }

    public function setComments(array $comments): PostDTO
    {
        $this->comments = $comments;

        return $this;
    }
}