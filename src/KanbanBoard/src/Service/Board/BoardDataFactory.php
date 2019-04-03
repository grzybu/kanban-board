<?php

declare(strict_types=1);

namespace KanbanBoard\Service\Board;

use Common\DI\FactoryInterface;
use Psr\Container\ContainerInterface;

class BoardDataFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get('Config\BoardConfig');

        $repositories = $container->get('Repository\Repository');
        $milestones = $container->get('Repository\Milestone');
        $issues = $container->get('Repository\Issue');



        return new BoardData($config, $repositories, $milestones, $issues);
    }
}
