<?php

declare(strict_types=1);

namespace KanbanBoard\Controller\Board;

use KanbanBoard\Service\Auth\AuthService;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

class BoardControllerFactoryTest extends \PHPUnit\Framework\TestCase
{
    private $container;
    private $mustacheEngine;
    private $response;
    private $authService;

    public function setUp()
    {
        parent::setUp();
        $this->container = $this->getMockBuilder(ContainerInterface::class)->disableOriginalConstructor()->getMock();

        $this->authService = $this->getMockBuilder(AuthService::class)->disableOriginalConstructor()->getMock();
        $this->mustacheEngine = $this->getMockBuilder(\Mustache_Engine::class)->disableOriginalConstructor()->getMock();
        $this->response = $this->getMockBuilder(Response::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * @test
     */
    public function itCanCreate()
    {
        $offset = 0;

        $this->container
            ->expects($this->at($offset++))
            ->method('get')
            ->with('Service\Auth')
            ->willReturn($this->authService);

        $this->container
            ->expects($this->at($offset++))
            ->method('get')
            ->with(\Mustache_Engine::class)
            ->willReturn($this->mustacheEngine);

        $this->container
            ->expects($this->at($offset++))
            ->method('get')
            ->with('Http\Response')
            ->willReturn($this->response);

        $controller = (new BoardControllerFactory())($this->container);
        $this->assertInstanceOf(BoardController::class, $controller);
    }
}
