<?php

declare(strict_types=1);

namespace KanbanBoard\Controller\Board;

use Psr\Container\ContainerInterface;

class BoardControllerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $authService = $container->get('Service\Auth');
        $mustacheEngine = $container->get(\Mustache_Engine::class);
        $repositoriesRepository = $container->get('Repository\Repository');
        $milestonesRepository = $container->get('Repository\Milestone');

        $response = $container->get('Http\Response');


        return new BoardController($authService, $mustacheEngine, $repositoriesRepository, $milestonesRepository, $response);
    }
}
