<?php

namespace App\Repository;

use App\Document\GameSession;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

class GameSessionRepository extends DocumentRepository
{
    public function __construct(DocumentManager $dm)
    {
        $uow = $dm->getUnitOfWork();
        $classMetadata = $dm->getClassMetadata(GameSession::class);
        parent::__construct($dm, $uow, $classMetadata);
    }

    public function save(GameSession $session): void
    {
        $this->getDocumentManager()->persist($session);
        $this->getDocumentManager()->flush();
    }

    public function findBySessionId(string $sessionId): ?GameSession
    {
        return $this->findOneBy(['sessionId' => $sessionId]);
    }

    public function findActiveSessions(): array
    {
        return $this->createQueryBuilder()
            ->field('completedAt')->equals(null)
            ->getQuery()
            ->execute()
            ->toArray();
    }

    public function findCompletedSessions(): array
    {
        return $this->createQueryBuilder()
            ->field('completedAt')->notEqual(null)
            ->sort('completedAt', 'desc')
            ->getQuery()
            ->execute()
            ->toArray();
    }

    public function findRecentSessions(int $limit = 10): array
    {
        return $this->createQueryBuilder()
            ->sort('startedAt', 'desc')
            ->limit($limit)
            ->getQuery()
            ->execute()
            ->toArray();
    }

    public function delete(GameSession $session): void
    {
        $this->getDocumentManager()->remove($session);
        $this->getDocumentManager()->flush();
    }
}