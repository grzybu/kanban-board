<?php

declare(strict_types=1);

namespace KanbanBoard\Read\Repository;

class Model
{
    protected $identifier;

    public function __construct(string  $identifier)
    {
        $this->identifier = $identifier;
    }

    public function getId(): string
    {
        return $this->identifier;
    }
}
