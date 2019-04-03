<?php

declare(strict_types=1);

namespace KanbanBoard\Controller\Board;

use KanbanBoard\Service\Auth\AuthService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use KanbanBoard\Service\Board\BoardData;

class BoardController
{
    private $mustacheEngine;
    private $response;
    protected $authService;
    protected $milestonesRepo;
    /**
     * @var BoardData
     */
    private $boardDataService;

    public function __construct(
        AuthService $authService,
        \Mustache_Engine $mustacheEngine,
        BoardData $boardDataService,
        Response $response
    ) {
        $this->authService = $authService;
        $this->mustacheEngine = $mustacheEngine;
        $this->response = $response;
        $this->boardDataService = $boardDataService;
    }

    public function __invoke()
    {
        if (!$this->authService->isAuthenticated()) {
            return $this->redirectToLogin();
        }

        $milestones = $this->boardDataService->getMilestones();

        $content = $this->mustacheEngine->render('index', ['milestones' => $milestones]);

        return $this->response->setContent($content);
    }

    protected function redirectToLogin(): RedirectResponse
    {
        return new RedirectResponse('/auth');
    }
}
