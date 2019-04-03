<?php

declare(strict_types=1);

namespace KanbanBoard\Controller\Board;

use Common\DI\FactoryInterface;
use Psr\Container\ContainerInterface;

class BoardControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container)
    {
        $authService = $container->get('Service\Auth');
        $mustacheEngine = $container->get(\Mustache_Engine::class);
        $boarDataService = $container->get('Service\BoardData');
        $response = $container->get('Http\Response');

        return new BoardController($authService, $mustacheEngine, $boarDataService, $response);
    }
}
