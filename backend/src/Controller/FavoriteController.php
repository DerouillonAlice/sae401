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
use OpenApi\Attributes as OA;

#[Route('/favorites')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class FavoriteController extends AbstractController
{
    #[OA\Get(
        path: '/favorites',
        summary: 'Liste des favoris',
        tags: ['Favoris'],
        responses: [
            new OA\Response(response: 200, description: 'Liste des favoris')
        ]
    )]
    #[Route('', name: 'favorite_list', methods: ['GET'])]
    public function list(FavoriteRepository $favoriteRepo): JsonResponse
    {
        $user = $this->getUser();
        $favorites = $favoriteRepo->findBy(['user' => $user]);

        return $this->json($favorites, 200, [], ['groups' => 'favorite:read']);
    }

    #[OA\Post(
        path: '/favorites',
        summary: 'Créer un favori',
        tags: ['Favoris'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'city', type: 'string'),
                    new OA\Property(property: 'latitude', type: 'number'),
                    new OA\Property(property: 'longitude', type: 'number'),
                    new OA\Property(property: 'position', type: 'integer'),
                    new OA\Property(property: 'showHumidity', type: 'boolean'),
                    new OA\Property(property: 'showPressure', type: 'boolean'),
                    new OA\Property(property: 'showWind', type: 'boolean'),
                    new OA\Property(property: 'showUV', type: 'boolean'),
                    new OA\Property(property: 'showSunCycle', type: 'boolean'),
                    new OA\Property(property: 'showVisibility', type: 'boolean'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Favori créé')
        ]
    )]
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

    #[OA\Put(
        path: '/favorites/{id}',
        summary: 'Mettre à jour un favori',
        tags: ['Favoris'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'showHumidity', type: 'boolean'),
                    new OA\Property(property: 'showPressure', type: 'boolean'),
                    new OA\Property(property: 'showWind', type: 'boolean'),
                    new OA\Property(property: 'showUV', type: 'boolean'),
                    new OA\Property(property: 'showSunCycle', type: 'boolean'),
                    new OA\Property(property: 'showVisibility', type: 'boolean'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Favori mis à jour')
        ]
    )]
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

    #[OA\Delete(
        path: '/favorites/{id}',
        summary: 'Supprimer un favori',
        tags: ['Favoris'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Favori supprimé')
        ]
    )]
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

    #[OA\Post(
        path: '/favorites/reorder',
        summary: 'Met à jour l\'ordre des favoris',
        tags: ['Favoris'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'array',
                items: new OA\Items(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer'),
                        new OA\Property(property: 'position', type: 'integer')
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Ordre mis à jour')
        ]
    )]
    #[Route('/reorder', name: 'favorite_reorder', methods: ['POST'])]
    public function reorder(Request $request, EntityManagerInterface $em, FavoriteRepository $favoriteRepo): JsonResponse
    {
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return $this->json(['error' => 'Invalid data'], 400);
        }

        foreach ($data as $favData) {
            $favorite = $favoriteRepo->find($favData['id'] ?? 0);
            if ($favorite && $favorite->getUser() === $user) {
                $favorite->setPosition($favData['position']);
            }
        }
        $em->flush();

        return $this->json(['message' => 'Ordre des favoris mis à jour']);
    }
}
