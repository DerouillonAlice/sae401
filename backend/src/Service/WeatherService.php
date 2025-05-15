<?php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class WeatherService
{
    private string $apiKey;

    public function __construct(
        private readonly HttpClientInterface $client,
        string $openweatherApiKey
    ) {
        $this->apiKey = $openweatherApiKey;
    }

    public function fetchWeather(string $city): array
    {
        $url = 'https://api.openweathermap.org/data/2.5/weather';

        $response = $this->client->request('GET', $url, [
            'query' => [
                'q' => $city,
                'appid' => $this->apiKey,
                'units' => 'metric',
                'lang' => 'fr',
            ],
        ]);

        return $response->toArray();
    }
}
