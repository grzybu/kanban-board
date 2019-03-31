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
        $response = $container->get('Http\Response');

        return new BoardController($authService, $mustacheEngine,  $response);
    }
}
