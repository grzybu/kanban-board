<?php

declare(strict_types=1);

namespace KanbanBoard\Controller\Auth;

use KanbanBoard\Service\Auth\AuthService;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthControllerFactoryTest extends \PHPUnit\Framework\TestCase
{
    private $container;
    private $authService;
    private $response;
    private $request;

    public function setUp()
    {
        parent::setUp();
        $this->container = $this->getMockBuilder(ContainerInterface::class)->disableOriginalConstructor()->getMock();
        $this->authService = $this->getMockBuilder(AuthService::class)->disableOriginalConstructor()->getMock();
        $this->request = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();
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
            ->with('Http\Request')
            ->willReturn($this->request);

        $this->container
            ->expects($this->at($offset++))
            ->method('get')
            ->with('Http\Response')
            ->willReturn($this->response);

        $controller = (new AuthControllerFactory())($this->container);
        $this->assertInstanceOf(AuthController::class, $controller);
    }
}
