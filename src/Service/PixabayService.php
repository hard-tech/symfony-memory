<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Response;

class PixabayService
{
    private const API_URL = 'https://pixabay.com/api/';
    private const API_VIDEO_URL = 'https://pixabay.com/api/videos/';
    private HttpClientInterface $httpClient;
    private string $apiKey;

    public function __construct(HttpClientInterface $httpClient, string $pixabayApiKey)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $pixabayApiKey;
    }

    public function searchImages(array $params = []): array
    {
        return $this->search(self::API_URL, $params);
    }

    public function searchSounds(array $params = []): array
    {
        return $this->search(self::API_VIDEO_URL, $params);
    }

    private function search(string $url, array $params): array
    {
        $defaultParams = [
            'key' => $this->apiKey,
            'safesearch' => 'true',
            'per_page' => 20,
        ];

        $response = $this->httpClient->request('GET', $url, [
            'query' => array_merge($defaultParams, $params),
        ]);

        if ($response->getStatusCode() !== Response::HTTP_OK) {
            throw new \RuntimeException('API request failed: ' . $response->getContent(false));
        }

        $data = $response->toArray();

        return $data['hits'] ?? [];
    }

    public function getImageById(int $id): ?array
    {
        $results = $this->searchImages(['id' => $id]);
        return $results[0] ?? null;
    }

    public function getSoundById(int $id): ?array
    {
        $results = $this->searchSounds(['id' => $id]);
        return $results[0] ?? null;
    }
}