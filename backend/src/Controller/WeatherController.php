<?php
namespace App\Controller;

use App\Service\WeatherService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Attributes as OA;

class WeatherController extends AbstractController
{
    public function __construct(private WeatherService $weatherService) {}

    #[OA\Get(
        path: '/weather/{city}',
        summary: 'Obtenir la météo actuelle pour une ville',
        tags: ['Météo'],
        parameters: [
            new OA\Parameter(name: 'city', in: 'path', required: true, schema: new OA\Schema(type: 'string'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Succès')
        ]
    )]
    #[Route('/weather/{city}', name: 'weather_current', methods: ['GET'])]
    public function current(string $city): JsonResponse
    {
        return $this->json($this->weatherService->fetchCurrentWeather($city));
    }

    #[OA\Get(
        path: '/forecast/{city}',
        summary: 'Obtenir la prévision météo pour une ville',
        tags: ['Météo'],
        parameters: [
            new OA\Parameter(name: 'city', in: 'path', required: true, schema: new OA\Schema(type: 'string'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Succès')
        ]
    )]
    #[Route('/forecast/{city}', name: 'weather_forecast', methods: ['GET'])]
    public function forecast(string $city): JsonResponse
    {
        return $this->json($this->weatherService->fetchForecast($city));
    }

    #[OA\Get(
        path: '/daily/{lat}/{lon}',
        summary: 'Prévision journalière par coordonnées',
        tags: ['Météo'],
        parameters: [
            new OA\Parameter(name: 'lat', in: 'path', required: true, schema: new OA\Schema(type: 'number')),
            new OA\Parameter(name: 'lon', in: 'path', required: true, schema: new OA\Schema(type: 'number'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Succès')
        ]
    )]
    #[Route('/daily/{lat}/{lon}', name: 'weather_daily', methods: ['GET'])]
    public function daily(float $lat, float $lon): JsonResponse
    {
        return $this->json($this->weatherService->fetchDailyForecast($lat, $lon));
    }

    #[OA\Get(
        path: '/search/{query}',
        summary: 'Recherche de ville',
        tags: ['Recherche'],
        parameters: [
            new OA\Parameter(name: 'query', in: 'path', required: true, schema: new OA\Schema(type: 'string'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Succès')
        ]
    )]
    #[Route('/search/{query}', name: 'weather_search', methods: ['GET'])]
    public function search(string $query): JsonResponse
    {
        return $this->json($this->weatherService->searchCity($query));
    }

    #[OA\Get(
        path: '/bycoords/{lat}/{lon}',
        summary: 'Météo actuelle par coordonnées',
        tags: ['Météo'],
        parameters: [
            new OA\Parameter(name: 'lat', in: 'path', required: true, schema: new OA\Schema(type: 'number')),
            new OA\Parameter(name: 'lon', in: 'path', required: true, schema: new OA\Schema(type: 'number'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Succès')
        ]
    )]
    #[Route('/bycoords/{lat}/{lon}', name: 'weather_by_coords', methods: ['GET'])]
    public function byCoords(float $lat, float $lon): JsonResponse
    {
        return $this->json($this->weatherService->fetchCurrentWeatherByCoords($lat, $lon));
    }

    #[OA\Get(
        path: '/byid/{id}',
        summary: 'Météo par ID de ville',
        tags: ['Météo'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Succès')
        ]
    )]
    #[Route('/byid/{id}', name: 'weather_by_id', methods: ['GET'])]
    public function byId(int $id): JsonResponse
    {
        return $this->json($this->weatherService->fetchWeatherByCityId($id));
    }
}