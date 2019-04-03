<?php

namespace KanbanBoard\Service\Board;

use KanbanBoard\Read\Milestone\Model as MilestoneModel;
use KanbanBoard\Read\Milestone\Repository as MilestonesRepository;
use KanbanBoard\Read\Repository\Repository as RepositoryRepository;
use KanbanBoard\Read\Issue\Repository as IssuesRepository;

class BoardData
{
    protected $pausedLabels;
    protected $account;
    protected $repositoriesRepo;
    protected $milestonesRepo;
    protected $issuesRepo;



    public function __construct(
        array $config,
        RepositoryRepository $repositoriesRepo,
        MilestonesRepository $milestonesRepo,
        IssuesRepository $issuesRepo
    ) {
        $this->repositoriesRepo = $repositoriesRepo;
        $this->milestonesRepo = $milestonesRepo;
        $this->issuesRepo = $issuesRepo;
        $this->account  = $config['account'];
        $this->pausedLabels = $config['pausedLabels'] ?? [];
    }

    public function getMilestones()
    {
        $repositories = $this->repositoriesRepo->getAll();
        $reposByMilestone = $this->getReposByMilestone($repositories);
        $milestones = [];

        $issues = [
            'queued' =>
                [
                    0 =>
                        [
                            'id' => 425993764,
                            'number' => 2,
                            'title' => 'pagination doesn\'t work',
                            'url' => 'https://github.com/grzybu/angular-charts/issues/2',
                            'assignee' => null,
                            'paused' =>
                                [
                                ],
                            'progress' =>
                                [
                                ],
                            'closed' => null,
                        ],
                ],
            'active' =>
                [
                    0 =>
                        [
                            'id' => 426142186,
                            'number' => 5,
                            'title' => 'test active issues',
                            'url' => 'https://github.com/grzybu/angular-charts/issues/5',
                            'assignee' => 'https://avatars2.githubusercontent.com/u/2069219?v=4?s=16',
                            'paused' =>
                                [
                                ],
                            'progress' =>
                                [
                                ],
                            'closed' => null,
                        ],
                ],
            'completed' =>
                [
                    0 =>
                        [
                            'id' => 425994317,
                            'number' => 3,
                            'title' => 'create e2e tests',
                            'url' => 'https://github.com/grzybu/angular-charts/issues/3',
                            'assignee' => null,
                            'paused' =>
                                [
                                ],

                            'progress' =>
                                [
                                ],
                            'closed' => '2019-03-27T19:22:08Z',
                        ],
                ],
        ];

        foreach ($reposByMilestone as $name => $milestone) {
            /** @var $milestone MilestoneModel */
            $issues = $this->getIssues($milestone->getRepository(), $milestone->getNumber());

            $percent = $milestone->getPercent();
            if (!empty($percent)) {
                $milestones[] = [
                    'milestone' => $name,
                    'url' => $milestone->getUrl(),
                    'progress' => $percent,
                    'queued' => $issues['queued'] ?? [],
                    'active' => $issues['active'] ?? [],
                    'completed' => $issues['completed'] ?? []
                ];
            }
        }
        return $milestones;
    }

    protected function getReposByMilestone(array $repositories): array
    {

        $reposByMilestone = [];

        foreach ($repositories as $repository) {
            $milestones = $this->milestonesRepo->getMilestones($this->account, $repository->getId());

            foreach ($milestones as $milestone) {
                /** @var $milestone MilestoneModel */
                $milestone->setRepository($repository->getId());
                $reposByMilestone[$milestone->getTitle()] = $milestone;
            }
        }

        ksort($reposByMilestone);

        return $reposByMilestone;
    }

    protected function getIssues($repository, $number)
    {
        $milestoneIssues = $this->issuesRepo->getIssues($this->account, $repository, $number);
        $issues = [];

        foreach ($milestoneIssues as $ii) {
            if (isset($ii['pull_request'])) {
                continue;
            }

            $issues[self::state($ii)][] = [
                'id' => $ii['id'],
                'number' => $ii['number'],
                'title' => $ii['title'],
                'url' => $ii['html_url'],
                'assignee' => $this->hasValue($ii, 'assignee') ? $ii['assignee']['avatar_url'] . '?s=16' : null,
                'paused' => $this->labelsMatch($ii, $this->pausedLabels),
                'progress' => $this->percent(
                    substr_count(strtolower($ii['body']), '[x]'),
                    substr_count(strtolower($ii['body']), '[ ]')
                ),
                'closed' => $ii['closed_at'] ?? null
            ];
        }

        if (isset($issues['active'])) {
            usort($issues['active'], function ($issueA, $issueB) {
                return count($issueA['paused']) - count($issueB['paused']) === 0 ?
                    strcmp($issueA['title'], $issueB['title']) :
                    count($issueA['paused']) - count($issueB['paused']);
            });
        }


        return $issues;
    }

    protected function hasValue(array $array, string $key): bool
    {
        return is_array($array) && array_key_exists($key, $array) && !empty($array[$key]);
    }
}
