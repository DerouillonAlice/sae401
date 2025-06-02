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

    // 1. Météo actuelle par nom de ville
    public function fetchCurrentWeather(string $city): array
    {
        return $this->makeRequest('/data/2.5/weather', ['q' => $city]);
    }

    // 2. Météo actuelle par coordonnées
    public function fetchCurrentWeatherByCoords(float $lat, float $lon): array
    {
        return $this->makeRequest('/data/2.5/weather', ['lat' => $lat, 'lon' => $lon]);
    }

    // 3. Prévisions 5 jours (toutes les 3h)
    public function fetchForecast(string $city): array
    {
        return $this->makeRequest('/data/2.5/forecast', ['q' => $city]);
    }

    // 4. Prévisions journalières
    public function fetchDailyForecast(float $lat, float $lon): array
    {
        return $this->makeRequest('/data/2.5/onecall', [
            'lat' => $lat,
            'lon' => $lon,
            'exclude' => 'minutely,hourly',
        ]);
    }

    // 5. Appel météo par ID de ville
    public function fetchWeatherByCityId(int $cityId): array
    {
        return $this->makeRequest('/data/2.5/weather', ['id' => $cityId]);
    }

    // 6. Recherche 
    public function searchCity(string $query): array
    {
        return $this->makeRequest('/geo/1.0/direct', [
            'q' => $query,
            'limit' => 5
        ], false);
    }


    // Fonction utilitaire pour faire la requête à l'API OpenWeatherMap

    private function makeRequest(string $endpoint, array $query, bool $includeUnits = true): array
    {
        $baseUrl = 'https://api.openweathermap.org';

        $defaultParams = [
            'appid' => $this->apiKey,
            'lang' => 'fr',
        ];

        if ($includeUnits) {
            $defaultParams['units'] = 'metric';
        }

        $response = $this->client->request('GET', $baseUrl . $endpoint, [
            'query' => array_merge($defaultParams, $query),
        ]);

        return $response->toArray();
    }
}