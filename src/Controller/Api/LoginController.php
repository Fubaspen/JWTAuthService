<?php

namespace App\Controller\Api;

use App\DTO\LoginRequest;
use App\Exception\ApiException;
use App\Service\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function index(#[MapRequestPayload] LoginRequest $loginRequest): JsonResponse
    {
        try {
            $token = $this->authService->authenticate($loginRequest);

            return $this->json(['token' => $token]);
        } catch (ApiException $e) {
            return $this->json(
                ['error' => $e->getMessage()],
                Response::HTTP_UNAUTHORIZED
            );
        }
    }
}
