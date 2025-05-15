<?php
namespace App\Controller;

use App\Service\WeatherService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WeatherController extends AbstractController
{
    public function __construct(private WeatherService $weatherService) {}

    #[Route('/weather/{city}', name: 'api_weather', methods: ['GET'])]
    public function getWeather(string $city): JsonResponse
    {
        try {
            $data = $this->weatherService->fetchWeather($city);
            return $this->json($data);
        } catch (\Throwable $e) {
            return $this->json(['error' => 'Erreur lors de l’appel météo'], 500);
        }
    }
}
