<?php


namespace App\DTO;


class AboutMeDTO extends DTO
{
    protected ?int $id = null;
    protected string $firstname;
    protected string $lastname;
    protected ?string $slogan = null;
    protected ?string $bio = null;
    protected ?string $profil_picture = null;
    protected ?string $cv_pdf = null;
    protected ?string $picture = null;
    protected ?string $twitter_link = null;
    protected ?string $linkedin_link = null;
    protected ?string $github_link = null;

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

    public function setId(?int $id): AboutMeDTO
    {
        $this->id = $id;
        
        return $this;
    }

    public function getFirstname(): string 
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): AboutMeDTO
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): AboutMeDTO
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getSlogan(): ?string
    {
        return $this->slogan;
    }

    public function setSlogan(?string $slogan): AboutMeDTO
    {
        $this->slogan = $slogan;

        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): AboutMeDTO
    {
        $this->bio = $bio;

        return $this;
    }

    public function getProfilPicture(): ?string
    {
        return $this->profil_picture;
    }

    public function setProfilPicture(?string $profil_picture): AboutMeDTO
    {
        $this->profil_picture = $profil_picture;

        return $this;
    }

    public function getCvPdf(): ?string
    {
        return $this->cv_pdf;
    }

    public function setCvPdf(?string $cv_pdf): AboutMeDTO
    {
        $this->cv_pdf = $cv_pdf;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): AboutMeDTO
    {
        $this->picture = $picture;

        return $this;
    }

    public function getTwitterLink(): ?string
    {
        return $this->twitter_link;
    }

    public function setTwitterLink(?string $twitter_link): AboutMeDTO
    {
        $this->twitter_link = $twitter_link;

        return $this;
    }

    public function getLinkedinLink(): ?string
    {
        return $this->linkedin_link;
    }

    public function setLinkedinLink(?string $linkedin_link): AboutMeDTO
    {
        $this->linkedin_link = $linkedin_link;

        return $this;
    }

    public function getGithubLink(): ?string
    {
        return $this->github_link;
    }

    public function setGithubLink(?string $github_link): AboutMeDTO
    {
        $this->github_link = $github_link;

        return $this;
    }
}