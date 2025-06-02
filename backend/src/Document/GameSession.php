<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document(collection: 'game_sessions')]
class GameSession
{
    #[MongoDB\Id]
    private ?string $id = null;

    #[MongoDB\Field(type: 'string')]
    private string $sessionId;

    #[MongoDB\Field(type: 'int')]
    private int $currentScore = 0;

    #[MongoDB\Field(type: 'int')]
    private int $totalQuestions = 0;

    #[MongoDB\Field(type: 'collection')]
    private array $answeredMovies = [];

    #[MongoDB\Field(type: 'date')]
    private \DateTime $startedAt;

    #[MongoDB\Field(type: 'date')]
    private ?\DateTime $completedAt = null;

    public function __construct()
    {
        $this->sessionId = uniqid('game_', true);
        $this->startedAt = new \DateTime();
    }

    // Getters and Setters
    public function getId(): ?string
    {
        return $this->id;
    }

    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    public function getCurrentScore(): int
    {
        return $this->currentScore;
    }

    public function setCurrentScore(int $currentScore): self
    {
        $this->currentScore = $currentScore;
        return $this;
    }

    public function getTotalQuestions(): int
    {
        return $this->totalQuestions;
    }

    public function setTotalQuestions(int $totalQuestions): self
    {
        $this->totalQuestions = $totalQuestions;
        return $this;
    }

    public function getAnsweredMovies(): array
    {
        return $this->answeredMovies;
    }

    public function addAnsweredMovie(string $movieId): self
    {
        $this->answeredMovies[] = $movieId;
        return $this;
    }

    public function getStartedAt(): \DateTime
    {
        return $this->startedAt;
    }

    public function getCompletedAt(): ?\DateTime
    {
        return $this->completedAt;
    }

    public function setCompletedAt(?\DateTime $completedAt): self
    {
        $this->completedAt = $completedAt;
        return $this;
    }
}