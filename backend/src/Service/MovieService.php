<?php

namespace App\Service;

use App\Document\Movie;
use App\Repository\MovieRepository;

class MovieService
{
    public function __construct(
        private MovieRepository $movieRepository
    ) {}

    public function getRandomMovie(array $excludeIds = []): ?Movie
    {
        return $this->movieRepository->findRandomMovie($excludeIds);
    }

    public function checkAnswer(string $movieId, int $guessedYear): ?array
    {
        $movie = $this->movieRepository->find($movieId);

        if (!$movie) {
            return null;
        }

        $actualYear = $movie->getYear();
        $isCorrect = $actualYear === $guessedYear;
        $difference = abs($actualYear - $guessedYear);

        // Score calculation: perfect = 100, -5 points per year off
        $score = $isCorrect ? 100 : max(0, 100 - ($difference * 5));

        return [
            'correct' => $isCorrect,
            'actualYear' => $actualYear,
            'guessedYear' => $guessedYear,
            'difference' => $difference,
            'score' => $score,
            'movie' => [
                'id' => $movie->getId(),
                'title' => $movie->getTitle(),
                'year' => $movie->getYear(),
                'director' => $movie->getDirector()
            ]
        ];
    }
}
