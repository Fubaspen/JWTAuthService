<?php

namespace App\Controller\Api;

use App\Service\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class VerifyController extends AbstractController
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    #[Route('/api/verify', name: 'api_verify_token', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $user = $this->authService->verifyAuthenticatedUser();

        return $this->json([
            'message' => 'Token is valid',
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
            ]
        ]);
    }
}
