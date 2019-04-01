<?php

namespace KanbanBoard\Repositories;


interface MilestoneRepositoryInterface
{
    public function getMilestones(string $account, string $repository): iterable;
}