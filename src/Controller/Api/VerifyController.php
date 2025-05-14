<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class VerifyController extends AbstractController
{
    #[Route('/api/verify', name: 'api_verify_token', methods: ['GET'])]
    public function index(): JsonResponse
    {
        // валидация в firewall
        return $this->json(['message' => 'Token is valid']);
    }
}