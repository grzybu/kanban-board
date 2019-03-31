<?php

declare(strict_types=1);

namespace KanbanBoard\Controller\Board;

use KanbanBoard\Service\Auth\AuthService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class BoardController
{
    private $mustacheEngine;
    private $response;
    protected $authService;

    public function __construct(AuthService $authService, \Mustache_Engine $mustacheEngine, Response $response)
    {
        $this->authService = $authService;
        $this->mustacheEngine = $mustacheEngine;
        $this->response = $response;
    }

    public function __invoke()
    {
        if (!$this->authService->isAuthenticated()) {
            return $this->redirectToLogin();
        }

        $data = [0 => ['milestone' => 'ver1', 'url' => 'https://github.com/grzybu/angular-charts/milestone/1', 'progress' => ['total' => 3, 'complete' => 1, 'remaining' => 2, 'percent' => 33.0,], 'queued' => [0 => ['id' => 425993764, 'number' => 2, 'title' => 'pagination doesn\'t work', 'url' => 'https://github.com/grzybu/angular-charts/issues/2', 'assignee' => null, 'paused' => [], 'progress' => [], 'closed' => null,],], 'active' => [0 => ['id' => 426142186, 'number' => 5, 'title' => 'test active issues', 'url' => 'https://github.com/grzybu/angular-charts/issues/5', 'assignee' => 'https://avatars2.githubusercontent.com/u/2069219?v=4?s=16', 'paused' => [], 'progress' => [], 'closed' => null,],], 'completed' => [0 => ['id' => 425994317, 'number' => 3, 'title' => 'create e2e tests', 'url' => 'https://github.com/grzybu/angular-charts/issues/3', 'assignee' => null, 'paused' => [], 'progress' => [], 'closed' => '2019-03-27T19:22:08Z',],],], 1 => ['milestone' => 'ver2', 'url' => 'https://github.com/grzybu/angular-charts/milestone/2', 'progress' => ['total' => 2, 'complete' => 0, 'remaining' => 2, 'percent' => 0.0,], 'queued' => [0 => ['id' => 425993434, 'number' => 1, 'title' => 'Create tests', 'url' => 'https://github.com/grzybu/angular-charts/issues/1', 'assignee' => null, 'paused' => [], 'progress' => [], 'closed' => null,],], 'active' => [0 => ['id' => 426141671, 'number' => 4, 'title' => 'tests', 'url' => 'https://github.com/grzybu/angular-charts/issues/4', 'assignee' => 'https://avatars2.githubusercontent.com/u/2069219?v=4?s=16', 'paused' => [], 'progress' => [], 'closed' => null,],], 'completed' => [],],];

        print "<pre>";
        var_dump($data);exit;
        $content = $this->mustacheEngine->render('index', ['milestones' => $data]);

        return $this->response->setContent($content);
    }

    protected function redirectToLogin(): RedirectResponse
    {
        return new RedirectResponse('/auth');
    }
}
