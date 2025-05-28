<?php

namespace App\Controller;

use App\Entity\Favorite;
use App\Repository\FavoriteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/favorites')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class FavoriteController extends AbstractController
{
    #[Route('', name: 'favorite_list', methods: ['GET'])]
    public function list(FavoriteRepository $favoriteRepo): JsonResponse
    {
        $user = $this->getUser();
        $favorites = $favoriteRepo->findBy(['user' => $user]);

        return $this->json($favorites, 200, [], ['groups' => 'favorite:read']);
    }

    #[Route('', name: 'favorite_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $favorite = new Favorite();

        $favorite->setUser($this->getUser());
        $favorite->setCity($data['city'] ?? '');
        $favorite->setLatitude($data['latitude'] ?? 0);
        $favorite->setLongitude($data['longitude'] ?? 0);
        $favorite->setPosition($data['position'] ?? 0);
        $favorite->setShowHumidity($data['showHumidity'] ?? false);
        $favorite->setShowPressure($data['showPressure'] ?? false);
        $favorite->setShowWind($data['showWind'] ?? false);
        $favorite->setShowUV($data['showUV'] ?? false);
        $favorite->setShowSunCycle($data['showSunCycle'] ?? false);
        $favorite->setShowVisibility($data['showVisibility'] ?? false);

        $em->persist($favorite);
        $em->flush();

        return $this->json($favorite, 201, [], ['groups' => 'favorite:read']);
    }

    #[Route('/{id}', name: 'favorite_update', methods: ['PUT'])]
    public function update(Favorite $favorite, Request $request, EntityManagerInterface $em): JsonResponse
    {
        if ($favorite->getUser() !== $this->getUser()) {
            return $this->json(['error' => 'Unauthorized'], 403);
        }

        $data = json_decode($request->getContent(), true);

        $favorite->setShowHumidity($data['showHumidity'] ?? $favorite->isShowHumidity());
        $favorite->setShowPressure($data['showPressure'] ?? $favorite->isShowPressure());
        $favorite->setShowWind($data['showWind'] ?? $favorite->isShowWind());
        $favorite->setShowUV($data['showUV'] ?? $favorite->isShowUV());
        $favorite->setShowSunCycle($data['showSunCycle'] ?? $favorite->isShowSunCycle());
        $favorite->setShowVisibility($data['showVisibility'] ?? $favorite->isShowVisibility());

        $em->flush();

        return $this->json($favorite, 200, [], ['groups' => 'favorite:read']);
    }

    #[Route('/{id}', name: 'favorite_delete', methods: ['DELETE'])]
    public function delete(Favorite $favorite, EntityManagerInterface $em): JsonResponse
    {
        if ($favorite->getUser() !== $this->getUser()) {
            return $this->json(['error' => 'Unauthorized'], 403);
        }

        $em->remove($favorite);
        $em->flush();

        return $this->json(['message' => 'Favorite deleted']);
    }
}
