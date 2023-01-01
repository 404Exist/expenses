<?php

declare(strict_types=1);

use App\Config;
use App\Middleware\AuthenticateMiddleware;
use App\Middleware\CsrfFieldsMiddleware;
use App\Middleware\ValidationExceptionMiddleware;
use App\Middleware\ValidationErrorsMiddleware;
use App\Middleware\OldFormDataMiddleware;
use App\Middleware\StartSessionsMiddleware;
use Slim\App;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

return function (App $app) {
    $container = $app->getContainer();
    $config    = $container->get(Config::class);

    // Csrf
    $app->add(CsrfFieldsMiddleware::class);
    $app->add('csrf');

    // Twig
    $app->add(TwigMiddleware::create($app, $container->get(Twig::class)));

    $app->add(ValidationExceptionMiddleware::class);
    $app->add(ValidationErrorsMiddleware::class);
    $app->add(OldFormDataMiddleware::class);
    $app->add(StartSessionsMiddleware::class);

    // Logger
    $app->addErrorMiddleware(
        $config->get('display_error_details'),
        $config->get('log_errors'),
        $config->get('log_error_details')
    );
};
