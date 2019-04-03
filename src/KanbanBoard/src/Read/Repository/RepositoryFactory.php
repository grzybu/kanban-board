<?php

declare(strict_types=1);

namespace KanbanBoard\Read\Repository;

use Common\DI\FactoryInterface;
use Psr\Container\ContainerInterface;

class RepositoryFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container)
    {
        $repositoriesConfig = $container->get('Config\Github.Repositories');

        $repositories = explode('|', $repositoriesConfig ?? []);

        return new Repository($repositories);
    }
}
