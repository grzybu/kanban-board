<?php

namespace KanbanBoard\Repositories\Milestone;


interface MilestoneRepositoryInterface
{
    public function getMilestones(string $account, string $repository): iterable;
}