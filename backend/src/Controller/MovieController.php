<?php

namespace App\Controller;

use App\Service\MovieService;
use App\Service\TmdbService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/movies')]
class MovieController extends AbstractController
{
    public function __construct(
        private MovieService $movieService,
        private TmdbService $tmdbService
    ) {}

    #[Route('/random', name: 'api_movie_random', methods: ['GET'])]
    public function getRandomMovie(Request $request): JsonResponse
    {
        $excludeIds = $request->query->all('exclude') ?: [];
        if (is_string($excludeIds)) {
            $excludeIds = explode(',', $excludeIds);
        }

        $movie = $this->movieService->getRandomMovie($excludeIds);

        if (!$movie) {
            return $this->json(['error' => 'No movies available'], 404);
        }

        return $this->json([
            'id' => $movie->getId(),
            'title' => $movie->getTitle(),
            'posterUrl' => $movie->getPosterUrl(),
            'genres' => $movie->getGenres()
        ]);
    }

    #[Route('/{id}/check', name: 'api_movie_check_answer', methods: ['POST'])]
    public function checkAnswer(string $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $guessedYear = $data['year'] ?? null;

        if (!$guessedYear) {
            return $this->json(['error' => 'Year is required'], 400);
        }

        $result = $this->movieService->checkAnswer($id, (int) $guessedYear);

        if (!$result) {
            return $this->json(['error' => 'Movie not found'], 404);
        }

        return $this->json($result);
    }

}