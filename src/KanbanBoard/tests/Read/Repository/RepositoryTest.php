<?php

namespace KanbanBoard\Read\Repository;

use PHPUnit\Framework\TestCase;

class RepositoryTest extends TestCase
{
    /**
     * @test
     */
    public function itCanRetunAll()
    {
        $data = ['test'];
        $repository = new Repository($data);

        $this->assertEquals([new Model($data[0])], $repository->getAll());
    }
}
