<?php

declare(strict_types=1);

namespace KanbanBoard\Controller\Auth;

use Psr\Container\ContainerInterface;

class AuthControllerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $authService = $container->get('Service\Auth');
        $request = $container->get('Http\Request');
        $response = $container->get('Http\Response');

        return new AuthController($authService, $request, $response);
    }
}
