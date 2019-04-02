<?php

declare(strict_types=1);

namespace KanbanBoard\Read\Repository;

class Repository implements RepositoryInterface
{
    protected $repositories;

    public function __construct(array $repositories = [])
    {
        foreach ($repositories as $repository)
        {
            $this->repositories[$repository] = new Model($repository);
        }
    }

    /**
     * @return Model[]
     */
    public function getAll()
    {
        return array_values($this->repositories);
    }
}
