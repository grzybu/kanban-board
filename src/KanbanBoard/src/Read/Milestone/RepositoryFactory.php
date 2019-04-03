<?php

namespace KanbanBoard\Read\Milestone;

use Common\DI\FactoryInterface;
use Psr\Container\ContainerInterface;

class RepositoryFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container)
    {
        $client = $container->get('Service\Github');
        $class = $this->getClass();

        return new Repository($client, $class);
    }

    protected function getClass()
    {
        return Model::class;
    }
}
