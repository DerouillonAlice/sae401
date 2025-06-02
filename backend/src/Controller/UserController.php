<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use OpenApi\Attributes as OA;

#[Route('/user')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class UserController extends AbstractController
{
    #[OA\Get(
        path: '/user/profile',
        summary: 'Consulter les informations du profil utilisateur',
        security: [['bearerAuth' => []]],
        tags: ['Utilisateur'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Profil utilisateur',
                content: new OA\JsonContent(ref: '#/components/schemas/User')
            )
        ]
    )]
    #[Route('/profile', name: 'user_profile', methods: ['GET'])]
    public function profile(): JsonResponse
    {
        return $this->json($this->getUser(), 200, [], ['groups' => 'user:read']);
    }

    #[OA\Put(
        path: '/user/profile',
        summary: 'Mettre à jour les préférences et informations utilisateur',
        security: [['bearerAuth' => []]],
        tags: ['Utilisateur'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'firstname', type: 'string'),
                    new OA\Property(property: 'email', type: 'string'),
                    new OA\Property(property: 'uniteTemperature', type: 'string'),
                    new OA\Property(property: 'unitePression', type: 'string'),
                    new OA\Property(property: 'uniteVent', type: 'string'),
                    new OA\Property(property: 'emailNotification', type: 'string')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Profil mis à jour')
        ]
    )]
    #[Route('/profile', name: 'user_profile_update', methods: ['PUT'])]
    public function update(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);

        if (isset($data['firstname'])) $user->setFirstname($data['firstname']);
        if (isset($data['email'])) $user->setEmail($data['email']);
        if (isset($data['uniteTemperature'])) $user->setUniteTemperature($data['uniteTemperature']);
        if (isset($data['unitePression'])) $user->setUnitePression($data['unitePression']);
        if (isset($data['uniteVent'])) $user->setUniteVent($data['uniteVent']);
        if (isset($data['emailNotification'])) $user->setEmailNotification($data['emailNotification']);

        $em->flush();

        return $this->json($user, 200, [], ['groups' => 'user:read']);
    }

    #[OA\Put(
        path: '/user/change-password',
        summary: 'Modifier le mot de passe utilisateur',
        security: [['bearerAuth' => []]],
        tags: ['Utilisateur'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'currentPassword', type: 'string'),
                    new OA\Property(property: 'newPassword', type: 'string')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Mot de passe modifié avec succès'),
            new OA\Response(response: 400, description: 'Mot de passe actuel incorrect')
        ]
    )]
    #[Route('/change-password', name: 'user_change_password', methods: ['PUT'])]
    public function changePassword(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em
    ): JsonResponse {
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);

        if (empty($data['currentPassword']) || empty($data['newPassword'])) {
            return $this->json(['error' => 'Champs manquants'], 400);
        }

        if (!$passwordHasher->isPasswordValid($user, $data['currentPassword'])) {
            return $this->json(['error' => 'Mot de passe actuel incorrect'], 400);
        }

        $user->setPassword($passwordHasher->hashPassword($user, $data['newPassword']));
        $em->flush();

        return $this->json(['message' => 'Mot de passe modifié avec succès']);
    }
}
