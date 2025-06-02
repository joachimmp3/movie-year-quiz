<?php

namespace App\Command;

use App\Document\Movie;
use App\Service\TmdbService;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:sync-tmdb',
    description: 'Synchronize movies from TMDB API to MongoDB'
)]
class SyncTmdbCommand extends Command
{
    public function __construct(
        private TmdbService $tmdbService,
        private DocumentManager $dm
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('pages', 'p', InputOption::VALUE_OPTIONAL, 'Number of pages to sync', 5)
            ->addOption('type', 't', InputOption::VALUE_OPTIONAL, 'Type of movies (popular, top_rated, discover)', 'popular')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force update existing movies');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $pages = (int) $input->getOption('pages');
        $type = $input->getOption('type');
        $force = $input->getOption('force');

        $io->title('TMDB Synchronization');
        $io->info(sprintf('Syncing %d pages of %s movies', $pages, $type));

        $totalMovies = 0;
        $newMovies = 0;
        $updatedMovies = 0;
        $skippedMovies = 0;

        for ($page = 1; $page <= $pages; $page++) {
            $io->section(sprintf('Processing page %d/%d', $page, $pages));

            try {
                $movies = match ($type) {
                    'popular' => $this->tmdbService->getPopularMovies($page),
                    'top_rated' => $this->tmdbService->getTopRatedMovies($page),
                    // 'discover' => $this->tmdbService->getDiscoverMovies(['page' => $page]),
                    default => throw new \InvalidArgumentException('Invalid movie type')
                };

                foreach ($movies['results'] as $movieData) {
                    $totalMovies++;
                    
                    if (!$this->isValidMovie($movieData)) {
                        $skippedMovies++;
                        continue;
                    }

                    $existingFilm = $this->dm->getRepository(Movie::class)
                        ->findOneBy(['tmdbId' => $movieData['id']]);

                    if ($existingFilm && !$force) {
                        $skippedMovies++;
                        continue;
                    }

                    $film = $existingFilm ?: new Movie();
                    $this->populateMovie($film, $movieData);

                    $this->dm->persist($film);

                    if ($existingFilm) {
                        $updatedMovies++;
                    } else {
                        $newMovies++;
                    }

                    // Flush every 50 movies to avoid memory issues
                    if (($totalMovies % 50) === 0) {
                        $this->dm->flush();
                        $this->dm->clear();
                    }
                }

                $this->dm->flush();
                $this->dm->clear();

            } catch (\Exception $e) {
                $io->error(sprintf('Error processing page %d: %s', $page, $e->getMessage()));
                continue;
            }
        }

        $io->success([
            sprintf('Synchronization completed!'),
            sprintf('Total movies processed: %d', $totalMovies),
            sprintf('New movies added: %d', $newMovies),
            sprintf('Movies updated: %d', $updatedMovies),
            sprintf('Movies skipped: %d', $skippedMovies)
        ]);

        return Command::SUCCESS;
    }

    private function isValidMovie(array $movieData): bool
    {
        return !empty($movieData['title']) &&
               !empty($movieData['release_date']) &&
               !empty($movieData['poster_path']) &&
               !$movieData['adult'];
    }

    private function populateMovie(Movie $movie, array $movieData): void
    {
        $releaseDate = new \DateTime($movieData['release_date']);
        $releaseYear = $releaseDate->format('Y');
        
        $movie->setTmdbId($movieData['id'])
             ->setTitle($movieData['title'])
             ->setYear($releaseYear)
             //->setOriginalTitle($movieData['original_title'])
             ->setPosterPath($movieData['poster_path'])
             ->setPosterUrl($this->tmdbService->buildPosterUrl($movieData['poster_path']))
             //->setReleaseDate($releaseDate)
             //->setOverview($movieData['overview'])
             ->setUpdatedAt(new \DateTime());
    }
}