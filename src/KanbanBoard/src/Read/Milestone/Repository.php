<?php

namespace KanbanBoard\Read\Milestone;

use KanbanBoard\Service\Github\Github as GithubService;

class Repository implements RepositoryInterface
{

    protected $githubService;
    protected $class;

    public function __construct(GithubService $githubService, string $class)
    {
        $this->githubService = $githubService;
        $this->class = $class;
    }

    protected function deserializeItem($item)
    {
        $class = $this->class;
        return $class::deserialize($item);
    }

    public function getMilestones(string $account, string $repository): iterable
    {
        $apiData = $this->githubService->getClient()->api('issues')->milestones()->all($account, $repository);

        $list = array_map([$this, 'deserializeItem'], $apiData);

        return $list;


    }


}
