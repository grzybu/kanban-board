<?php

declare(strict_types=1);

namespace KanbanBoard\Controller\Auth;

use KanbanBoard\Service\Auth\AuthService;
use KanbanBoard\Service\Auth\StateVerifyException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController
{
    private $authService;
    private $request;
    private $response;

    public function __construct(AuthService $authService, Request $request, Response $response)
    {
        $this->authService = $authService;
        $this->request = $request;
        $this->response = $response;
    }

    public function __invoke()
    {
        if ($this->authService->isAuthenticated()) {
            return new RedirectResponse('/');
        }

        try {
            $code = $this->request->get('code', null);
            $state = $this->request->get('state', null);
            if ($code) {
                if ($this->authService->login($code, $state)) {
                    return new RedirectResponse('/');
                }
            } else {
                $this->authService->requestIdentity();
            }
        } catch (StateVerifyException $exception) {
            return $this->response->setStatusCode(403)->setContent(
                $exception->getMessage()
                . PHP_EOL
                . '<a href="/auth">Click to login again</a>'

            );
        } catch (\RuntimeException $exception) {
            return $this->response->setStatusCode(403)->setContent($exception->getMessage());
        }
    }
}
