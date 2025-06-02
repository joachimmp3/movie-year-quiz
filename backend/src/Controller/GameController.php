<?php

namespace App\Controller;

use App\Service\GameService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/game')]
class GameController extends AbstractController
{
    public function __construct(
        private GameService $gameService
    ) {}

    #[Route('/start', name: 'api_game_start', methods: ['POST'])]
    public function startGame(): JsonResponse
    {
        $session = $this->gameService->startNewGame();

        return $this->json([
            'sessionId' => $session->getSessionId(),
            'score' => $session->getCurrentScore(),
            'totalQuestions' => $session->getTotalQuestions()
        ]);
    }

    #[Route('/{sessionId}/submit', name: 'api_game_submit_answer', methods: ['POST'])]
    public function submitAnswer(string $sessionId, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $movieId = $data['movieId'] ?? null;
        $guessedYear = $data['year'] ?? null;

        if (!$movieId || !$guessedYear) {
            return $this->json(['error' => 'MovieId and year are required'], 400);
        }

        $result = $this->gameService->submitAnswer($sessionId, $movieId, (int) $guessedYear);

        if (!$result) {
            return $this->json(['error' => 'Game session not found'], 404);
        }

        return $this->json($result);
    }

    #[Route('/{sessionId}', name: 'api_game_get_session', methods: ['GET'])]
    public function getGameSession(string $sessionId): JsonResponse
    {
        $session = $this->gameService->getGameSession($sessionId);

        if (!$session) {
            return $this->json(['error' => 'Game session not found'], 404);
        }

        return $this->json([
            'sessionId' => $session->getSessionId(),
            'score' => $session->getCurrentScore(),
            'totalQuestions' => $session->getTotalQuestions(),
            'answeredMovies' => $session->getAnsweredMovies()
        ]);
    }
}