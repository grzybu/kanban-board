<?php

declare(strict_types=1);

namespace KanbanBoard\Controller\Auth;

use Common\DI\FactoryInterface;
use Psr\Container\ContainerInterface;

class AuthControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container)
    {
        $authService = $container->get('Service\Auth');
        $request = $container->get('Http\Request');
        $response = $container->get('Http\Response');

        return new AuthController($authService, $request, $response);
    }
}
