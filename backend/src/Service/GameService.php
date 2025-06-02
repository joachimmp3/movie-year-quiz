<?php

namespace App\Service;

use App\Document\GameSession;
use App\Repository\GameSessionRepository;

class GameService
{
    public function __construct(
        private GameSessionRepository $gameSessionRepository,
        private MovieService $movieService
    ) {}

    public function startNewGame(): GameSession
    {
        $session = new GameSession();
        $this->gameSessionRepository->save($session);

        return $session;
    }

    public function getGameSession(string $sessionId): ?GameSession
    {
        return $this->gameSessionRepository->findBySessionId($sessionId);
    }

    public function submitAnswer(string $sessionId, string $movieId, int $guessedYear): ?array
    {
        $session = $this->getGameSession($sessionId);

        if (!$session) {
            return null;
        }

        // Check the answer
        $result = $this->movieService->checkAnswer($movieId, $guessedYear);

        if (!$result) {
            return null;
        }

        // Update session
        $session->addAnsweredMovie($movieId);
        $session->setCurrentScore($session->getCurrentScore() + $result['score']);
        $session->setTotalQuestions($session->getTotalQuestions() + 1);

        $this->gameSessionRepository->save($session);

        return array_merge($result, [
            'sessionScore' => $session->getCurrentScore(),
            'totalQuestions' => $session->getTotalQuestions()
        ]);
    }
}