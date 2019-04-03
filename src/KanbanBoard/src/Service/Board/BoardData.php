<?php

namespace KanbanBoard\Service\Board;

use KanbanBoard\Read\Issue\Model as IssueModel;
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
        $this->account = $config['account'];
        $this->pausedLabels = $config['pausedLabels'] ?? [];
    }

    public function getMilestones()
    {
        $repositories = $this->repositoriesRepo->getAll();
        $reposByMilestone = $this->getReposByMilestone($repositories);
        $milestones = [];

        foreach ($reposByMilestone as $name => $milestone) {
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

    /**
     * @param array $repositories
     * @return MilestoneModel[]
     */
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

        foreach ($milestoneIssues as $issue) {
            /**  @var $issue IssueModel */
            if ($issue->isPullRequest()) {
                continue;
            }
            $issues[$issue->getBoardState()][] = [
                'id' => $issue->getId(),
                'number' => $issue->getNumber(),
                'title' => $issue->getTitle(),
                'url' => $issue->getHtmlUrl(),
                'assignee' => $issue->getAssigneeAvatar(),
                'paused' => $issue->hasLabel($this->pausedLabels),
                'progress' => $issue->getProgress(),
                'closed' => $issue->isClosed(),
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
}
