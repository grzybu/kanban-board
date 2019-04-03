<?php

declare(strict_types=1);

namespace KanbanBoard\Service\Board;

use Common\DI\FactoryInterface;
use Psr\Container\ContainerInterface;

class BoardDataFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container)
    {
        $repositories = $container->get('Repository\Repository');
        $milestones = $container->get('Repository\Milestone');
        $issues = $container->get('Repository\Issue');

        $config = $container->get('Config\BoardConfig');


        return new BoardData($config, $repositories, $milestones, $issues);
    }
}
