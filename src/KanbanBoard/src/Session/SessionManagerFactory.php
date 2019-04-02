<?php

declare(strict_types=1);

namespace KanbanBoard\Session;

use DI\FactoryInterface;
use Psr\Container\ContainerInterface;

class SessionManagerFactory
{
    public function __invoke(ContainerInterface $container)
    {

        return new SessionManager();
    }
}
