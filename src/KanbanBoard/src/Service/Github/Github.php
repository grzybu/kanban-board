<?php

namespace KanbanBoard\Service\Github;

use KanbanBoard\Service\Auth\AuthService;
use Github\Client as GithubClient;

class Github
{
    protected $githubClient;
    protected $authService;

    public function __construct(GithubClient $githubClient, AuthService $authService)
    {
        $this->githubClient = $githubClient;
        $this->authService = $authService;
    }

    public function getClient(): GithubClient
    {
        if (!$this->authService->isAuthenticated()) {
            throw new \RuntimeException('Not authenticated');
        }
        $this->githubClient->authenticate($this->authService->getToken(), GithubClient::AUTH_HTTP_TOKEN);

        return $this->githubClient;
    }
}
