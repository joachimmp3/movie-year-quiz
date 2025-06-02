<?php

namespace App\Repository;

use App\Document\Movie;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

class MovieRepository extends DocumentRepository
{

    public function save(Movie $movie): void
    {
        $this->getDocumentManager()->persist($movie);
        $this->getDocumentManager()->flush();
    }

    public function findRandomMovie(array $excludeIds = []): ?Movie
    {
        // Premier QueryBuilder pour compter
        $countQb = $this->createQueryBuilder();
        
        if (!empty($excludeIds)) {
            $countQb->field('id')->notIn($excludeIds);
        }

        // Get total count
        $total = $countQb->count()->getQuery()->execute();
        
        if ($total === 0) {
            return null;
        }

        // Get random offset
        $randomOffset = rand(0, $total - 1);

        // Nouveau QueryBuilder pour la requête finale
        $qb = $this->createQueryBuilder();
        
        if (!empty($excludeIds)) {
            $qb->field('id')->notIn($excludeIds);
        }

        return $qb->skip($randomOffset)->limit(1)->getQuery()->getSingleResult();
    }

    public function findByTmdbId(string $tmdbId): ?Movie
    {
        return $this->findOneBy(['tmdbId' => $tmdbId]);
    }

    public function findByYear(int $year): array
    {
        return $this->findBy(['year' => $year]);
    }

    public function findByYearRange(int $startYear, int $endYear): array
    {
        return $this->createQueryBuilder()
            ->field('year')->gte($startYear)
            ->field('year')->lte($endYear)
            ->getQuery()
            ->execute()
            ->toArray();
    }
}