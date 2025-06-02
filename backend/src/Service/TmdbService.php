<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TmdbService
{
    public function __construct(
        #[Autowire('%env(TMDB_API_KEY)%')] private string $tmdbApiKey,
        #[Autowire('%env(TMDB_BASE_URL)%')] private string $tmdbBaseUrl,
        #[Autowire('%env(TMDB_IMAGE_BASE_URL)%')] private string $tmdbImageBaseUrl,
        private HttpClientInterface $httpClient
    ) {}   

    private function getHeaders(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->tmdbApiKey,
            'accept' => 'application/json'
        ];
    }

    public function getPopularMovies(int $page = 1): array
    {
        $response = $this->httpClient->request('GET', $this->tmdbBaseUrl . '/movie/popular', [
            'headers' => $this->getHeaders(),
            'query' => [
                'page' => $page,
                'language' => 'en-US'
            ]
        ]);

        return $response->toArray();
    }

    public function getTopRatedMovies(int $page = 1): array
    {
        $response = $this->httpClient->request('GET', $this->tmdbBaseUrl . '/movie/top_rated', [
            'headers' => $this->getHeaders(),
            'query' => [
                'page' => $page,
                'language' => 'en-US'
            ]
        ]);

        return $response->toArray();
    }

    public function getDiscoverMovies(array $params = []): array
    {
        $defaultParams = [
            'sort_by' => 'popularity.desc',
            'include_adult' => false,
            'include_video' => false,
            'page' => 1,
            'language' => 'en-US'
        ];

        $response = $this->httpClient->request('GET', $this->tmdbBaseUrl . '/discover/movie', [
            'headers' => $this->getHeaders(),
            'query' => array_merge($defaultParams, $params)
        ]);

        return $response->toArray();
    }

    public function getMovieDetails(int $movieId): array
    {
        $response = $this->httpClient->request('GET', $this->tmdbBaseUrl . "/movie/{$movieId}", [
            'headers' => $this->getHeaders(),
            'query' => [
                'language' => 'en-US'
            ]
        ]);

        return $response->toArray();
    }

    public function getConfiguration(): array
    {
        $response = $this->httpClient->request('GET', $this->tmdbBaseUrl . '/configuration', [
            'headers' => $this->getHeaders()
        ]);

        return $response->toArray();
    }

    public function buildPosterUrl(string $posterPath, string $size = 'w500'): string
    {
        return $this->tmdbImageBaseUrl . "/{$size}{$posterPath}";
    }
}