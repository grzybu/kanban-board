<?php

namespace KanbanBoard\Read\Milestone;

use Common\Read\DeserializableRepository;
use KanbanBoard\Service\Github\Github as GithubService;

class Repository extends DeserializableRepository implements RepositoryInterface
{
    protected $githubService;

    public function __construct(GithubService $githubService, string $class)
    {
        $this->githubService = $githubService;
        parent::__construct($class);
    }

    public function getMilestones(string $account, string $repository): iterable
    {
        $apiData = $this->githubService->getClient()->api('issues')->milestones()->all($account, $repository);
        return parent::deserializeItems($apiData);
    }
}
