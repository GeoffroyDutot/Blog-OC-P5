<?php


namespace App\DTO;


use DateTime;

class PostDTO extends DTO {
    protected int $id;
    protected string $title;
    protected string $slug;
    protected DateTime $created_at;
    protected DateTime $updated_at;
    protected string $content;
    protected string $resume;
    protected string $picture;

    public function __construct($data)
    {
        if ($data) {
            $this->hydrate($data);
        }
    }

    public function getId(): int {
        return $this->id;
    }

    public function setId(int $id): PostDTO {
        $this->id = $id;
        return $this;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function setTitle(string $title): PostDTO {
        $this->title = $title;
        return $this;
    }

    public function getSlug(): string {
        return $this->slug;
    }

    public function setSlug(string $slug): PostDTO {
        $this->slug = $slug;
        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        if (!$this->created_at instanceof DateTime) {
            $this->created_at = new DateTime($this->created_at);
        }

        return $this->created_at;
    }

    public function setCreatedAt(string $created_at): PostDTO {
        if (!$created_at instanceof DateTime) {
            $created_at = new DateTime($created_at);
        }

        $this->created_at = $created_at;

        return $this;
    }

    public function __set($key, $value)
    {
        return $this->$key($value);
    }

    public function getUpdatedAt(): DateTime {
        return $this->updated_at;
    }

    public function setUpdatedAt(string $updated_at): PostDTO {
        if (!$updated_at instanceof DateTime) {
            $updated_at = new DateTime($updated_at);
        }

        $this->updated_at = $updated_at;

        return $this;
    }

    public function getContent(): string {
        return $this->content;
    }

    public function setContent(string $content): PostDTO {
        $this->content = $content;
        return $this;
    }

    public function getResume(): string {
        return $this->resume;
    }

    public function setResume(string $resume): PostDTO {
        $this->resume = $resume;
        return $this;
    }

    public function getPicture(): string {
        return $this->picture;
    }

    public function setPicture(string $picture): PostDTO {
        $this->picture = $picture;
        return $this;
    }
}