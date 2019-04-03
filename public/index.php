<?php
die("test");
chdir(dirname(__DIR__));
require 'vendor/autoload.php';

/**
 * Self-called anonymous function that creates its own scope and keep the global namespace clean.
 */
call_user_func(function () {
    /** @var \DI\Container $container */
    $container = require 'config/container.php';

    /** @var \KanbanBoard\Session\SessionManager $sessionManager */
    $sessionManager = $container->get('SessionManager');
    $sessionManager->start();
    if (!$sessionManager->isValid()) {
        $sessionManager->forget();
    }

    $dotenv = Dotenv\Dotenv::create('.');
    $dotenv->safeLoad();

    try {
        $dotenv->required(['GH_CLIENT_ID', 'GH_CLIENT_SECRET', 'GH_ACCOUNT', 'GH_REPOSITORIES'])->notEmpty();
    } catch (RuntimeException $exception) {
        die($exception->getMessage());
    }

    $dispatcher = $container->get('Dispatcher');

    /** @var \Symfony\Component\HttpFoundation\Request $request */
    $request = $container->get('Http\Request');

    /** @var \Symfony\Component\HttpFoundation\Response $request */
    $response = $container->get('Http\Response');


    $routeInfo = $dispatcher->dispatch($request->getMethod(), $request->getPathInfo());

    switch ($routeInfo[0]) {
        case \FastRoute\Dispatcher::NOT_FOUND:
            $response->setContent('404 - Page not found');
            $response->setStatusCode(404);
            break;
        case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
            $response->setContent('405 - Method not allowed');
            $response->setStatusCode(405);
            break;
        case \FastRoute\Dispatcher::FOUND:

            $requireAuth = $routeInfo[1][1] ?? false;
            /* @var $authService \KanbanBoard\Service\Auth\AuthService */
            $authService = $container->get('Service\Auth');

            if ($requireAuth === \Common\Router\Section::PROTECTED && !$authService->isAuthenticated()) {
                    $handler ='Controller\AuthController';
            } else {
                $handler = $routeInfo[1][0];
            }

            $vars = $routeInfo[2];

            /** @var \Symfony\Component\HttpFoundation\Response $response */
            $response = call_user_func($container->get($handler), $vars);

            $response->send();
            break;
    }
});
