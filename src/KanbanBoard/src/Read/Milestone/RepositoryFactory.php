<?php

namespace KanbanBoard\Read\Milestone;
use Psr\Container\ContainerInterface;

class RepositoryFactory
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
