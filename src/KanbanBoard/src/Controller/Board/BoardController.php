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
    protected $milestonesRepo;
    /**
     * @var BoardData
     */
    private $boardDataService;

    public function __construct(
        BoardData $boardDataService, \Mustache_Engine $mustacheEngine, Response $response
    ) {
        $this->mustacheEngine = $mustacheEngine;
        $this->response = $response;
        $this->boardDataService = $boardDataService;
    }

    public function __invoke()
    {
        $milestones = $this->boardDataService->getMilestones();

        $content = $this->mustacheEngine->render('index', ['milestones' => $milestones]);

        return $this->response->setContent($content);
    }
}
