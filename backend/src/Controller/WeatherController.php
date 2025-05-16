<?php
namespace App\Controller;

use App\Service\WeatherService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WeatherController extends AbstractController
{
    public function __construct(private WeatherService $weatherService) {}

    #[Route('/weather/{city}', name: 'weather_current', methods: ['GET'])]
    public function current(string $city): JsonResponse
    {
        return $this->json($this->weatherService->fetchCurrentWeather($city));
    }

    #[Route('/forecast/{city}', name: 'weather_forecast', methods: ['GET'])]
    public function forecast(string $city): JsonResponse
    {
        return $this->json($this->weatherService->fetchForecast($city));
    }

    #[Route('/daily/{lat}/{lon}', name: 'weather_daily', methods: ['GET'])]
    public function daily(float $lat, float $lon): JsonResponse
    {
        return $this->json($this->weatherService->fetchDailyForecast($lat, $lon));
    }

    #[Route('/search/{query}', name: 'weather_search', methods: ['GET'])]
    public function search(string $query): JsonResponse
    {
        return $this->json($this->weatherService->searchCity($query));
    }

    #[Route('/bycoords/{lat}/{lon}', name: 'weather_by_coords', methods: ['GET'])]
    public function byCoords(float $lat, float $lon): JsonResponse
    {
        return $this->json($this->weatherService->fetchCurrentWeatherByCoords($lat, $lon));
    }

    #[Route('/byid/{id}', name: 'weather_by_id', methods: ['GET'])]
    public function byId(int $id): JsonResponse
    {
        return $this->json($this->weatherService->fetchWeatherByCityId($id));
    }
}