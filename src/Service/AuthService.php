<?php

namespace App\Service;

use App\DTO\LoginRequest;
use App\Entity\User;
use App\Exception\ApiException;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly JWTTokenManagerInterface $jwtManager,
        private readonly Security $security,
    ) {}

    public function authenticate(LoginRequest $loginRequest): string
    {
        /** @var UserRepository $userRepo */
        $userRepo = $this->entityManager->getRepository(User::class);
        $user = $userRepo->findOneByEmail($loginRequest->email);

        if (!$user || !$this->passwordHasher->isPasswordValid($user, $loginRequest->password)) {
            throw new ApiException(401,'Invalid credentials.');
        }

        return $this->jwtManager->create($user);
    }

    public function verifyAuthenticatedUser(): User
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new ApiException(401, 'Unauthorized');
        }

        if (method_exists($user, 'isActive') && !$user->isActive()) {
            throw new ApiException(403, 'User is inactive or blocked');
        }

        return $user;
    }
}


