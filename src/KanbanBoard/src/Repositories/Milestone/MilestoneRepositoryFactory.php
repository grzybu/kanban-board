<?php

namespace KanbanBoard\Repositories\Milestone;
use Psr\Container\ContainerInterface;

class MilestoneRepositoryFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new MilestoneRepository();
    }


}