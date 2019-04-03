<?php

declare(strict_types=1);

namespace KanbanBoard\Controller\Board;

use Common\DI\FactoryInterface;
use Psr\Container\ContainerInterface;

class BoardControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container)
    {
        $boarDataService = $container->get('Service\BoardData');
        $mustacheEngine = $container->get(\Mustache_Engine::class);
        $response = $container->get('Http\Response');

        return new BoardController($boarDataService, $mustacheEngine, $response);
    }
}
