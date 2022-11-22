<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Contracts\AuthInterface;
use App\DataObjects\RegisterUserData;
use App\Entity\User;
use App\Exceptions\ValidationException;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class AuthController
{
    public function __construct(
        private readonly Twig $twig,
        private readonly EntityManager $entityManager,
        private readonly AuthInterface $auth,
    ) {
    }

    public function loginView(Request $request, Response $response): Response
    {
        return $this->twig->render($response, 'auth/login.twig');
    }

    public function registerView(Request $request, Response $response): Response
    {
        return $this->twig->render($response, 'auth/register.twig');
    }

    public function register(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        if (validate($this->registerValidateRules(), $data)) {
            $this->auth->register(
                new RegisterUserData($data['name'], $data['email'], $data['password'])
            );
        }

        return $response->withHeader("Location", "/")->withStatus(302);
    }

    public function login(Request $request, Response $response): Response
    {
        validate([
            ['required', [['email', 'password']]],
            ['email', 'email']
        ]);

        if (! $this->auth->attempt($request->getParsedBody())) {
            throw new ValidationException(["password" => ["You have entered an invalid username or password"]]);
        }

        return $response->withHeader("Location", "/")->withStatus(302);
    }

    public function logout(Request $request, Response $response): Response
    {
        $this->auth->logout();

        return $response->withHeader("Location", "/login")->withStatus(302);
    }

    protected function registerValidateRules(): array
    {
        return [
            ['required', [['name', 'email', 'password', 'confirmPassword']]],
            ['email', 'email'],
            ['lengthMin', ['password', 8]],
            ['equals', ['confirmPassword', 'password'], ['label' => 'Confirm Password']],
            [
                fn($field, $value) => ! $this->entityManager->getRepository(User::class)->count(
                    ['email' => $value]
                ),
                'email',
                ['message' => 'User with the given email address already exists']
            ],
        ];
    }
}
