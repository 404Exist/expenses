<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Views\Twig;

class CsrfFieldsMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly Twig $twig,
        private readonly ContainerInterface $container,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $csrf = $this->container->get("csrf");
        $fields = <<<CSRF_FIELDS
            <input type="hidden" name="{$csrf->getTokenNameKey()}" value="{$csrf->getTokenName()}">
            <input type="hidden" name="{$csrf->getTokenValueKey()}" value="{$csrf->getTokenValue()}">
        CSRF_FIELDS;

        $this->twig->getEnvironment()->addGlobal('csrf', $fields);

        return $handler->handle($request);
    }
}
