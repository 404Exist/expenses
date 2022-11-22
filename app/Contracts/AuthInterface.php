<?php

declare(strict_types=1);

namespace App\Contracts;

use App\DataObjects\RegisterUserData;

interface AuthInterface
{
    public function user(): ?Authenticatable;

    public function attempt(array $credentials): bool;

    public function checkCredentials(Authenticatable $user, array $credentials): bool;

    public function logout(): void;

    public function register(RegisterUserData $data): Authenticatable;

    public function login(Authenticatable $user): void;
}
