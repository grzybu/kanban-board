<?php

namespace KanbanBoard\Read\Issue;

interface RepositoryInterface
{
    public function getIssues(string $account, string $repository, string $milestoneId): iterable;
}
