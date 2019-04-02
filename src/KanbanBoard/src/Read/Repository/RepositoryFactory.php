<?php

declare(strict_types=1);

namespace KanbanBoard\Read\Repository;

use Psr\Container\ContainerInterface;

class RepositoryFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $repositoriesConfig = $container->get('Config\Github.Repositories');

        $repositories = explode('|', $repositoriesConfig ?? []);

        return new Repository($repositories);
    }

}
