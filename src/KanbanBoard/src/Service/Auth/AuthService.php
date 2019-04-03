<?php
declare(strict_types=1);

namespace KanbanBoard\Service\Auth;

use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Request;
use Common\Session\SessionManager;

class AuthService
{
    protected $config;
    protected $httpClient;
    protected $request;
    protected $sessionManager;

    // move to configuration
    protected $githubAuthUrl = 'https://github.com/login/oauth/authorize';


    public function __construct(array $config, Client $httpClient, Request $request, SessionManager $sessionManager)
    {
        $this->config = $config;
        $this->httpClient = $httpClient;
        $this->request = $request;
        $this->sessionManager = $sessionManager;
    }


    public function getToken(): ?string
    {
        return $this->sessionManager->get('gh-token');
    }

    protected function setToken(string $token): self
    {
        $this->sessionManager->put('gh-token', $token);
        return $this;
    }

    public function isAuthenticated(): bool
    {
        return !empty($this->sessionManager->get('gh-token'));
    }

    // Generate a random hash and store in the session for security
    protected function getState(): string
    {
        $state = $this->sessionManager->get('state');
        if (!$state) {
            $state = bin2hex(random_bytes(32));
            $this->sessionManager->put('state', $state);
        }

        return $state;
    }

    protected function verifyState(string $returnedState): bool
    {
        if (!$returnedState || ($returnedState !== $this->getState())) {
            return false;
        }
        return true;
    }

    protected function clearState(): void
    {
        $this->sessionManager->put('state', null);
    }

    /**
     * Request a user's GitHub identity
     */
    public function requestIdentity()
    {
        $queryParams = [
            'client_id' => $this->config['clientId'],
            'scope' => 'repo',
            'state' => $this->getState(),
            'redirect_uri' => $this->config['redirectUri'] ?? $this->request->getUri()
        ];
        
        header('Location: ' . $this->githubAuthUrl . '?' . http_build_query($queryParams));
    }

    public function getAccessToken(string $code, string $state): ?string
    {
        $client = $this->httpClient;

        $postData = [
            'client_id' => $this->config['clientId'],
            'client_secret' => $this->config['clientSecret'],
            'state' => $state,
            'redirect_uri' => $this->config['redirectUri'] ?? $this->request->getUri(),
            'code' => $code
        ];

        $response = $client->request(
            'POST',
            'https://github.com/login/oauth/access_token',
            [
                'query' => http_build_query($postData),
                'headers' => [
                    'Accept' => 'application/json'
                ]
            ]
        );


        $decoded = json_decode($response->getBody()->getContents());


        $token = $decoded->access_token ?? null;
        $error = $decoded->error ?? null;

        if ($error) {
            throw  new \RuntimeException($decoded->error_description ?? 'Unknown GH API Error');
        }

        if (null === $token) {
            throw  new \RuntimeException('Couldn\'t receive token');
        }

        return $token;
    }

    public function login(string $code, string $state): bool
    {
        if ($this->isAuthenticated()) {
            return true;
        }


        if (!$this->verifyState($state)) {
            $this->clearState();
            throw new StateVerifyException('Could not verify state param.');
        }

        $token = $this->getAccessToken($code, $state);

        $this->setToken($token);
        $this->clearState();
        return true;
    }
}
