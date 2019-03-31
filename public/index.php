<?php

chdir(dirname(__DIR__));
require 'vendor/autoload.php';
/*
use KanbanBoard\GithubClient;


$repositories = explode('|', getenv('GH_REPOSITORIES'));
$authentication = new \KanbanBoard\Authentication();
$token = $authentication->login();
$github = new GithubClient($token, getenv('GH_ACCOUNT'));
$board = new \KanbanBoard\Application($github, $repositories, ['waiting-for-feedback']);
$data = $board->board();
$m = new Mustache_Engine([
    'loader' => new Mustache_Loader_FilesystemLoader('src/views'),
]);

echo $m->render('index', ['milestones' => $data]);


exit;*/

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
    $dotenv->load();

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
            $handler = $routeInfo[1][0];
            $requireAuth  = $routeInfo[1][1] ?? false;

            $vars = $routeInfo[2];

            /** @var \Symfony\Component\HttpFoundation\Response $response */
            $response = call_user_func($container->get($handler), $vars);

            $response->send();
            break;
    }
});
