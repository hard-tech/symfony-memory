<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class PixabayService
{
    private string $apiKey;
    private HttpClientInterface $httpClient;

    public function __construct(string $pixabayApiKey, HttpClientInterface $httpClient)
    {
        $this->apiKey = $pixabayApiKey;
        $this->httpClient = $httpClient;
    }


    public function searchImages(string $query, int $perPage = 20): array
    {
        return $this->search('https://pixabay.com/api/', [
            'q' => $query,
            'image_type' => 'photo',
            'per_page' => $perPage,
        ]);
    }

    public function searchSounds(string $query, int $perPage = 20): array
    {
        return $this->search('https://pixabay.com/api/videos/', [
            'q' => $query,
            'video_type' => 'animation',
            'per_page' => $perPage,
        ]);
    }

    private function search(string $url, array $params): array
    {
        $response = $this->httpClient->request('GET', $url, [
            'query' => array_merge(['key' => $this->apiKey], $params),
        ]);

        return $response->toArray()['hits'] ?? [];
    }
}