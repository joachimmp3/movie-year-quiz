<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use App\Repository\MovieRepository;
use DateTime;

#[MongoDB\Document(repositoryClass: MovieRepository::class)]
class Movie
{
    #[MongoDB\Id]
    private ?string $id = null;

    #[MongoDB\Field(type: 'string')]
    private ?string $tmdbId = null;

    #[MongoDB\Field(type: 'string')]
    private string $title;

    #[MongoDB\Field(type: 'int')]
    private int $year;

    #[MongoDB\Field(type: 'string')]
    private string $posterUrl;

    #[MongoDB\Field(type: 'string')]
    private string $posterPath;

    #[MongoDB\Field(type: 'collection')]
    private array $genres = [];

    #[MongoDB\Field(type: 'string')]
    private ?string $director = null;

    #[MongoDB\Field(type: 'string')]
    private ?string $overview = null;

    #[MongoDB\Field(type: 'float')]
    private ?float $rating = null;

    #[MongoDB\Field(type: 'date')]
    private \DateTime $createdAt;

    #[MongoDB\Field(type: 'date')]
    private \DateTime $updatedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    // Getters and Setters
    public function getId(): ?string
    {
        return $this->id;
    }

    public function getTmdbId(): ?string
    {
        return $this->tmdbId;
    }

    public function setTmdbId(?string $tmdbId): self
    {
        $this->tmdbId = $tmdbId;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function setYear(int $year): self
    {
        $this->year = $year;
        return $this;
    }

    public function getPosterUrl(): string
    {
        return $this->posterUrl;
    }

    public function setPosterUrl(string $posterUrl): self
    {
        $this->posterUrl = $posterUrl;
        return $this;
    }

    public function getPosterPath(): string
    {
        return $this->posterPath;
    }

    public function setPosterPath(string $posterPath): self
    {
        $this->posterPath = $posterPath;
        return $this;
    }

    public function getGenres(): array
    {
        return $this->genres;
    }

    public function setGenres(array $genres): self
    {
        $this->genres = $genres;
        return $this;
    }

    public function getDirector(): ?string
    {
        return $this->director;
    }

    public function setDirector(?string $director): self
    {
        $this->director = $director;
        return $this;
    }

    public function getOverview(): ?string
    {
        return $this->overview;
    }

    public function setOverview(?string $overview): self
    {
        $this->overview = $overview;
        return $this;
    }

    public function getRating(): ?float
    {
        return $this->rating;
    }

    public function setRating(?float $rating): self
    {
        $this->rating = $rating;
        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
