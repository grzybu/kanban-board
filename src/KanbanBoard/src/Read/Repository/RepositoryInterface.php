<?php

namespace KanbanBoard\Read\Repository;

use KanbanBoard\Read\Repository\Model as RepositoryModel;

interface RepositoryInterface
{
    /**
     * @return RepositoryModel[]
     */
    public function getAll();
}
