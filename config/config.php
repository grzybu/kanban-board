<?php

use function DI\create;

return [

    // simple configurattion
    'Config\Github.Auth' => [
        'clientId' => DI\env('GH_CLIENT_ID'),
        'clientSecret' => DI\env('GH_CLIENT_SECRET'),
    ],

    'Config\Github.Repositories' => DI\env('GH_REPOSITORIES', []),

    'Config\Github.Account' => DI\env('Account'),

    'SessionManager' => function () {
        return new \KanbanBoard\Session\SessionManager('KanbanBoard', 'KNBSESSIONID');
    },

    Mustache_Engine::class => function () {
        $loader = new Mustache_Loader_FilesystemLoader('src/views');
        return new Mustache_Engine(['loader' => $loader]);
    },
    //controllers
    'Controller\DefaultController' => DI\Factory(\KanbanBoard\Controller\Board\BoardControllerFactory::class),
    'Controller\AuthController' => DI\Factory(\KanbanBoard\Controller\Auth\AuthControllerFactory::class),

    //services
    'Service\Auth' => DI\Factory(\KanbanBoard\Service\Auth\AuthServiceFactory::class),

    'Service\Github' => DI\Factory(KanbanBoard\Service\Github\GithubFactory::class),

    //repositories
    'Repository\Milestone' => DI\Factory(\KanbanBoard\Read\Milestone\RepositoryFactory::class),
    'Repository\Repository' => DI\Factory(\KanbanBoard\Read\Repository\RepositoryFactory::class),



    'Dispatcher' => function () {
        $routes = function (\FastRoute\RouteCollector $r) {
            $routes = include('routes.php');
            foreach ($routes as $route) {
                $r->addRoute($route[0], $route[1], $route[2]);
            }
        };
        return \FastRoute\simpleDispatcher($routes);
    },

    'Http\Request' => function () {
        return \Symfony\Component\HttpFoundation\Request::createFromGlobals();
    },

    'Http\Response' => function () {
        return new  \Symfony\Component\HttpFoundation\Response(
            'Content',
            \Symfony\Component\HttpFoundation\Response::HTTP_OK,
            ['content-type' => 'text/html']
        );
    },

];
