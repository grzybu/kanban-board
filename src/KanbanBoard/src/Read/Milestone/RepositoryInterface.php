<?php

namespace KanbanBoard\Read\Milestone;

interface RepositoryInterface
{
    public function getMilestones(string $account, string $repository): iterable;
}
