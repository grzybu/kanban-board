<?php
/**
 * Created by PhpStorm.
 * User: grzybu
 * Date: 02.04.19
 * Time: 15:01
 */

namespace KanbanBoard\Read\Milestone;

use KanbanBoard\Service\Github\Github;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class RepositoryFactoryTest extends TestCase
{
    /** @var MockObject */
    private $container;
    private $client;

    public function setUp()
    {
        parent::setUp();
        $this->container = $this->getMockBuilder(ContainerInterface::class)->disableOriginalConstructor()->getMock();
        $this->client = $this->getMockBuilder(Github::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * @test
     */
    public function itCanCreate()
    {
        $this->container->expects($this->at(0))
            ->method('get')
            ->with('Service\Github')
            ->willReturn($this->client);

        $factory = new RepositoryFactory();
        $repository = $factory($this->container);

        $this->assertInstanceOf(Repository::class, $repository);
    }
}
