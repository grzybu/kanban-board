<?php

namespace KanbanBoard\Service\Github;

use Github\Client;
use KanbanBoard\Service\Auth\AuthService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\SimpleCache\CacheInterface;

class GithubFactoryTest extends TestCase
{
    /** @var MockObject */
    private $container;

    private $cacheInterface;

    private $client;

    /** @var MockObject */
    private $service;

    public function setUp()
    {
        parent::setUp();
        $this->container = $this->getMockBuilder(ContainerInterface::class)->disableOriginalConstructor()->getMock();
        $this->cacheInterface = $this->getMockBuilder(CacheInterface::class)->disableOriginalConstructor()->getMock();
        $this->client = $this->getMockBuilder(Client::class)->disableOriginalConstructor()->getMock();
        $this->service = $this->getMockBuilder(AuthService::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * @test
     */
    public function itCanCreate()
    {
        $this->container->expects($this->at(0))
            ->method('get')
            ->with('Service\Auth')
            ->willReturn($this->service);

        $factory = new GithubFactory();

        $github = $factory($this->container);

        $this->assertInstanceOf(Github::class, $github);
    }
}
