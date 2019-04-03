<?php

namespace KanbanBoard\Service\Board;

use KanbanBoard\Read\Milestone\Repository as MilestonesRepository;
use KanbanBoard\Read\Repository\Repository as RepositoryRepository;
use KanbanBoard\Read\Repository\Repository as IssuesRepository;


use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class BoardDataFactoryTest extends TestCase
{
    /** @var MockObject */
    protected $container;

    protected $repositories;

    protected $milestones;

    protected $issues;

    /**
     * @test
     */
    public function setUp()
    {
        parent::setUp();

        $this->container = $this->getMockBuilder(ContainerInterface::class)->disableOriginalConstructor()->getMock();

        $this->repositories = $this->getMockBuilder(RepositoryRepository::class)->disableOriginalConstructor()->getMock();

        $this->milestones = $this->getMockBuilder(MilestonesRepository::class)->disableOriginalConstructor()->getMock();

        $this->issues = $this->getMockBuilder(RepositoryRepository::class)->disableOriginalConstructor()->getMock();


        $index = 0;
        $this->container->expects($this->at($index++))
            ->method('get')
            ->with('Repository\Repository')
            ->willReturn($this->repositories);

        $this->container->expects($this->at($index++))
            ->method('get')
            ->with('Repository\Milestone')
            ->willReturn($this->milestones);

        $this->container->expects($this->at($index++))
            ->method('get')
            ->with('Repository\Issue')
            ->willReturn($this->issues);

        $this->container->expects($this->at($index++))
            ->method('get')
            ->with('Config\Github.PausedLabels')
            ->willReturn(['waiting-for-feedback']);

        $factory = new BoardDataFactory();

        $boardData = $factory($this->container);

        $this->assertInstanceOf(BoardData::class, $boardData);
    }
}
