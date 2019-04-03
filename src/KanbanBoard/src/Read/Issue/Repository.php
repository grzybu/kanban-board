<?php

namespace KanbanBoard\Read\Issue;

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

    public function getIssues(string $account, string $repository, string $milestone): iterable
    {
        $issueParameters = ['milestone' => $milestone, 'state' => 'all'];

        $apiData = $this->githubService->getClient()->api('issue')->all($account, $repository, $issueParameters);
        return parent::deserializeItems($apiData);
    }
}
