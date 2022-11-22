<?php

declare(strict_types=1);

namespace App\Custom;

use App\Contracts\Authenticatable;
use App\Contracts\AuthInterface;
use App\Contracts\SessionInterface;
use App\DataObjects\RegisterUserData;
use App\Entity\User;
use Doctrine\ORM\EntityManager;

class Auth implements AuthInterface
{
    private ?Authenticatable $user = null;

    public function __construct(
        private readonly EntityManager $entityManager,
        private readonly SessionInterface $session
    ) {
    }

    public function user(): ?Authenticatable
    {
        if ($this->user !== null) {
            return $this->user;
        }

        $userId = $this->session->get('user');

        if (! $userId) {
            return null;
        }

        $user = $this->entityManager->find(User::class, $userId);

        if (! $user) {
            return null;
        }

        $this->user = $user;

        return $this->user;
    }

    public function attempt(array $credentials): bool
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $credentials['email']]);

        if (! $this->checkCredentials($user, $credentials)) {
            return false;
        }

        $this->login($user);

        return true;
    }

    public function checkCredentials(Authenticatable $user, array $credentials): bool
    {
        return $user && password_verify($credentials['password'], $user->getPassword());
    }

    public function logout(): void
    {
        $this->session->forget('user');
        $this->session->regenerate();

        $this->user = null;
    }

    public function register(RegisterUserData $data): Authenticatable
    {
        $user = new User();

        $user->setName($data->name);
        $user->setEmail($data->email);
        $user->setPassword(encrypt($data->password));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->logIn($user);

        return $user;
    }

    public function login(Authenticatable $user): void
    {
        $this->session->regenerate();

        $this->session->put('user', $user->getId());

        $this->user = $user;
    }
}
