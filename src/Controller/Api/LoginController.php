<?php

namespace App\Controller\Api;

use App\DTO\LoginRequest;
use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Doctrine\ORM\EntityManagerInterface;

class LoginController extends AbstractController
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly JWTTokenManagerInterface    $jwtManager,
        private readonly EntityManagerInterface      $entityManager
    ) {
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function index(#[MapRequestPayload] LoginRequest $loginRequest): JsonResponse
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy(['email' => $loginRequest->email]);

        if (!$user) {
            throw new BadCredentialsException('Invalid credentials.');
        }

        if (!$this->passwordHasher->isPasswordValid($user, $loginRequest->password)) {
            throw new BadCredentialsException('Invalid credentials.');
        }

        $token = $this->jwtManager->create($user);

        return $this->json(['token' => $token]);
    }
}