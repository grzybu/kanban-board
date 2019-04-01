<?php

use function DI\create;

return [

    // simple configs
    'Config\Github.Auth' => [
        'clientId' => DI\env('GH_CLIENT_ID'),
        'clientSecret' => DI\env('GH_CLIENT_SECRET'),
    ],
    'Config\Github.Repositories' => [
        'GH_ACCOUNT' => DI\env('GH_ACCOUNT'),
        'GH_REPOSITORIES' => DI\env('GH_REPOSITORIES'),
    ],

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

    'Service\Github\Client' => function () {
        $filesystemAdapter = new \League\Flysystem\Adapter\Local(sys_get_temp_dir() . '/github-api-cache');
        $filesystem = new \League\Flysystem\Filesystem($filesystemAdapter);
        $cachePool = new \Cache\Adapter\Filesystem\FilesystemCachePool($filesystem);

        $client = new Github\Client();
        $client->addCache($cachePool);
        return new $client;
    },

    //repositories
    'Repository/Milestone' => DI\Factory(\KanbanBoard\Read\Model\Milestone\RepositoryFactory::class),

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
