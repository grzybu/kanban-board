<?php

declare(strict_types=1);

namespace KanbanBoard\Controller\Board;

use KanbanBoard\Read\Milestone\Model as MilestoneModel;
use KanbanBoard\Read\Repository\Repository as RepositoriesRepository;
use KanbanBoard\Read\Milestone\Repository as MilestonesRepository;
use KanbanBoard\Service\Auth\AuthService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class BoardController
{
    private $mustacheEngine;
    private $response;
    protected $authService;
    protected $repositoryRepo;
    protected $milestonesRepo;

    public function __construct(AuthService $authService, \Mustache_Engine $mustacheEngine, RepositoriesRepository $repositoryRepository, MilestonesRepository $milestonesRepo,  Response $response)
    {
        $this->authService = $authService;
        $this->mustacheEngine = $mustacheEngine;
        $this->repositoryRepo = $repositoryRepository;
        $this->milestonesRepo = $milestonesRepo;
        $this->response = $response;
    }

    public function __invoke()
    {
        if (!$this->authService->isAuthenticated()) {
            return $this->redirectToLogin();
        }

        print "<pre>";
        $repositories  = $this->repositoryRepo->getAll();

        $reposByMilestone = $this->getReposByMilestone($repositories);


        $milestones = [];


        foreach ($reposByMilestone as $name => $data) {
            $issues = []; //$this->issues($data['repository'], $data['number']);
            $percent = $data->getPercent();
            if (!empty($percent)) {
                $milestones[] = [
                    'milestone' => $name,
                    'url' => $data['html_url'],
                    'progress' => $percent,
                    'queued' => $issues['queued'] ?? [],
                    'active' => $issues['active'] ?? [],
                    'completed' => $issues['completed'] ?? []
                ];
            }
        }

        var_dump($milestones);



        $data = [0 => ['milestone' => 'ver1', 'url' => 'https://github.com/grzybu/angular-charts/milestone/1', 'progress' => ['total' => 3, 'complete' => 1, 'remaining' => 2, 'percent' => 33.0,], 'queued' => [0 => ['id' => 425993764, 'number' => 2, 'title' => 'pagination doesn\'t work', 'url' => 'https://github.com/grzybu/angular-charts/issues/2', 'assignee' => null, 'paused' => [], 'progress' => [], 'closed' => null,],], 'active' => [0 => ['id' => 426142186, 'number' => 5, 'title' => 'test active issues', 'url' => 'https://github.com/grzybu/angular-charts/issues/5', 'assignee' => 'https://avatars2.githubusercontent.com/u/2069219?v=4?s=16', 'paused' => [], 'progress' => [], 'closed' => null,],], 'completed' => [0 => ['id' => 425994317, 'number' => 3, 'title' => 'create e2e tests', 'url' => 'https://github.com/grzybu/angular-charts/issues/3', 'assignee' => null, 'paused' => [], 'progress' => [], 'closed' => '2019-03-27T19:22:08Z',],],], 1 => ['milestone' => 'ver2', 'url' => 'https://github.com/grzybu/angular-charts/milestone/2', 'progress' => ['total' => 2, 'complete' => 0, 'remaining' => 2, 'percent' => 0.0,], 'queued' => [0 => ['id' => 425993434, 'number' => 1, 'title' => 'Create tests', 'url' => 'https://github.com/grzybu/angular-charts/issues/1', 'assignee' => null, 'paused' => [], 'progress' => [], 'closed' => null,],], 'active' => [0 => ['id' => 426141671, 'number' => 4, 'title' => 'tests', 'url' => 'https://github.com/grzybu/angular-charts/issues/4', 'assignee' => 'https://avatars2.githubusercontent.com/u/2069219?v=4?s=16', 'paused' => [], 'progress' => [], 'closed' => null,],], 'completed' => [],],];


        var_dump($data);exit;
        $content = $this->mustacheEngine->render('index', ['milestones' => $data]);

        return $this->response->setContent($content);
    }


    protected function getReposByMilestone(array $repositories): array
    {
        $reposByMilestone = [];

        foreach ($repositories as $repository) {
            $milestones = $this->milestonesRepo->getMilestones('grzybu', $repository->getId());
            foreach ($milestones as $milestone) {
                /** @var $milestone MilestoneModel */

                $milestone->setRepository($repository->getId());
                $reposByMilestone[$milestone->getTitle()] = $milestone;
            }
        }

        ksort($reposByMilestone);

        return $reposByMilestone;
    }

    protected function redirectToLogin(): RedirectResponse
    {
        return new RedirectResponse('/auth');
    }
}
