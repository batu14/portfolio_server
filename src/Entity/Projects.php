<?php

namespace App\Entity;

use App\Repository\ProjectsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectsRepository::class)]
class Projects
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $clientName = null;

    #[ORM\Column(length: 255)]
    private ?string $Timeline = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $Tecs = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $github = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $demo = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $publishYear = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $images = null;

    #[ORM\Column]
    private ?bool $isActive = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getClientName(): ?string
    {
        return $this->clientName;
    }

    public function setClientName(string $clientName): static
    {
        $this->clientName = $clientName;

        return $this;
    }

    public function getTimeline(): ?string
    {
        return $this->Timeline;
    }

    public function setTimeline(string $Timeline): static
    {
        $this->Timeline = $Timeline;

        return $this;
    }

    public function getTecs(): ?string
    {
        return $this->Tecs;
    }

    public function setTecs(string $Tecs): static
    {
        $this->Tecs = $Tecs;

        return $this;
    }

    public function getGithub(): ?string
    {
        return $this->github;
    }

    public function setGithub(string $github): static
    {
        $this->github = $github;

        return $this;
    }

    public function getDemo(): ?string
    {
        return $this->demo;
    }

    public function setDemo(string $demo): static
    {
        $this->demo = $demo;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPublishYear(): ?string
    {
        return $this->publishYear;
    }

    public function setPublishYear(string $publishYear): static
    {
        $this->publishYear = $publishYear;

        return $this;
    }

    public function getImages(): ?string
    {
        return $this->images;
    }

    public function setImages(string $images): static
    {
        $this->images = $images;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }
}
