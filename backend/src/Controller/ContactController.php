<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Attributes as OA;


class ContactController extends AbstractController
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
    #[Route('/contact', name: 'api_contact', methods: ['POST'])]
    public function contact(Request $request, MailerInterface $mailer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $name = $data['name'] ?? null;
        $email = $data['email'] ?? null;
        $message = $data['message'] ?? null;

        if (!$name || !$email || !$message) {
            return new JsonResponse(['error' => 'Tous les champs sont requis.'], 400);
        }

        $emailMessage = (new Email())
            ->from($email)
            ->to('sae401.tpe@gmail.com')
            ->subject('Nouveau message de contact')
            ->text("Nom : $name\nEmail : $email\n\nMessage :\n$message");

        try {
            $mailer->send($emailMessage);
            return new JsonResponse(['message' => 'Message envoyé avec succès.']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors de l’envoi du message.'], 500);
        }
    }
}
