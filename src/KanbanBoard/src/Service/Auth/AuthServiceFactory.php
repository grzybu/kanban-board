<?php

declare(strict_types=1);

namespace KanbanBoard\Service\Auth;

use Psr\Container\ContainerInterface;
use GuzzleHttp\Client;

class AuthServiceFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $githubAuth = $container->get('Config\Github.Auth');
        $request = $container->get('Http\Request');
        $sessionManager = $container->get('SessionManager');

        $client = $this->createHttpClient();

        return new AuthService($githubAuth, $client, $request, $sessionManager);
    }

    protected function createHttpClient(): Client
    {
        return new Client();
    }
}
