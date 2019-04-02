<?php

namespace KanbanBoard\Read\Repository;

use KanbanBoard\Service\Github\Github;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class RepositoryFactoryTest extends TestCase
{
    /** @var MockObject */
    private $container;

    public function setUp()
    {
        parent::setUp();
        $this->container = $this->getMockBuilder(ContainerInterface::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * @test
     */
    public function itCanCreate()
    {

        $repositories = 'repo-1|repo-2';

        $this->container->expects($this->at(0))
            ->method('get')
            ->with('Config\Github.Repositories')
            ->willReturn($repositories);


        $factory = new RepositoryFactory();
        $repository = $factory($this->container);

        $this->assertInstanceOf(Repository::class, $repository);
    }
}
