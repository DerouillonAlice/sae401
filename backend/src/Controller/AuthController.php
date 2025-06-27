<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use App\Repository\UserRepository;
use OpenApi\Attributes as OA;
use SymfonyCasts\Bundle\ResetPassword\Exception\TooManyPasswordRequestsException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AuthController extends AbstractController
{
    #[OA\Post(
        path: '/register',
        summary: 'Créer un nouvel utilisateur',
        tags: ['Authentification'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'email', type: 'string', example: 'user@example.com'),
                    new OA\Property(property: 'password', type: 'string', example: 'password123'),
                    new OA\Property(property: 'firstname', type: 'string', example: 'Alice'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Utilisateur créé'),
            new OA\Response(response: 400, description: 'Champs manquants')
        ]
    )]
    #[Route('/register', name: 'api_register', methods: ['POST'])]
    public function register(Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email'], $data['password'], $data['firstname'])) {
            return new JsonResponse(['error' => 'Missing fields'], 400);
        }

        $user = new User();
        $user->setEmail($data['email']);
        $user->setFirstname($data['firstname']);
        $user->setPassword($hasher->hashPassword($user, $data['password']));

        $em->persist($user);
        $em->flush();

        return new JsonResponse(['message' => 'User registered successfully'], 201);
    }

    #[OA\Post(
        path: '/login_check',
        summary: 'Authentification utilisateur (JWT)',
        tags: ['Authentification'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'username', type: 'string', example: 'user@example.com'),
                    new OA\Property(property: 'password', type: 'string', example: 'password123'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Token JWT retourné'),
            new OA\Response(response: 401, description: 'Identifiants invalides')
        ]
    )]
    #[Route('/login_check', name: 'api_login_check', methods: ['POST'])]
    public function loginCheck()
    {
        // Ce endpoint est géré par LexikJWTAuthenticationBundle, pas besoin de code ici
    }

    #[OA\Post(
        path: '/request-reset-password',
        summary: 'Demander la réinitialisation du mot de passe',
        tags: ['Authentification'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'email', type: 'string', example: 'user@example.com')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Token de reset envoyé'),
            new OA\Response(response: 404, description: 'Utilisateur non trouvé')
        ]
    )]
    #[Route('/request-reset-password', name: 'api_request_reset_password', methods: ['POST'])]
    public function requestResetPassword(
        Request $request,
        UserRepository $userRepository,
        ResetPasswordHelperInterface $resetHelper,
        MailerInterface $mailer,
        UrlGeneratorInterface $urlGenerator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;

        if (!$email) {
            return new JsonResponse(['error' => 'Email is required'], 400);
        }

        $user = $userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        try {
            $resetToken = $resetHelper->generateResetToken($user);
        } catch (TooManyPasswordRequestsException $e) {
            return new JsonResponse([
                'error' => 'Une demande a déjà été envoyée récemment. Veuillez vérifier votre boîte mail ou réessayer plus tard.'
            ], 429);
        }

        // URL vers le front Vue avec le token en query
        $frontendUrl = $_ENV['FRONTEND_URL'] ?? 'http://localhost:5173';
        $resetUrl = $frontendUrl . '/reset-password?token=' . $resetToken->getToken();
        

        // Création et envoi de l'e-mail
        $emailMessage = (new Email())
            ->from('no-reply@tonsite.com') // à modifier avec ton domaine
            ->to($user->getEmail())
            ->subject('Réinitialisation de votre mot de passe')
            ->text("Bonjour,\n\nPour réinitialiser votre mot de passe, cliquez sur le lien suivant :\n$resetUrl\n\nCe lien expirera dans une heure.");

        $mailer->send($emailMessage);

        return new JsonResponse([
            'message' => 'Un email a été envoyé pour réinitialiser votre mot de passe.'
        ]);
    }

    #[OA\Post(
        path: '/reset-password',
        summary: 'Réinitialiser le mot de passe',
        tags: ['Authentification'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'token', type: 'string'),
                    new OA\Property(property: 'password', type: 'string')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Mot de passe réinitialisé'),
            new OA\Response(response: 400, description: 'Token ou mot de passe manquant/invalide')
        ]
    )]
    #[Route('/reset-password', name: 'api_reset_password', methods: ['POST'])]
    public function resetPassword(
        Request $request,
        UserPasswordHasherInterface $hasher,
        ResetPasswordHelperInterface $resetHelper,
        EntityManagerInterface $em
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $token = $data['token'] ?? null;
        $newPassword = $data['password'] ?? null;

        if (!$token || !$newPassword) {
            return new JsonResponse(['error' => 'Token and new password are required'], 400);
        }

        try {
            $user = $resetHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            return new JsonResponse(['error' => 'Invalid or expired token'], 400);
        }

        $user->setPassword($hasher->hashPassword($user, $newPassword));
        $resetHelper->removeResetRequest($token);

        $em->flush();

        return new JsonResponse(['message' => 'Password has been reset successfully']);
    }
}
