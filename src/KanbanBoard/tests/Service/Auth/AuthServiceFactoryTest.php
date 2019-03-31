<?php

namespace KanbanBoard\Service\Auth;

use GuzzleHttp\Client;
use KanbanBoard\Session\SessionManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class AuthServiceFactoryTest extends TestCase
{
    /** @var MockObject */
    private $container;

    /** @var MockObject */
    private $httpClient;

    /** @var MockObject */
    private $request;

    /** @var MockObject */
    private $sessionManager;

    public function setUp()
    {
        parent::setUp();
        $this->container = $this->getMockBuilder(ContainerInterface::class)->disableOriginalConstructor()->getMock();
        $this->httpClient = $this->getMockBuilder(Client::class)->disableOriginalConstructor()->getMock();
        $this->request = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();
        $this->sessionManager = $this->getMockBuilder(SessionManager::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * @test
     */
    public function itCanCreateService()
    {
        $config = [
            'clientId'     => 'TEST-1',
            'clientSecret' => 'TEST-Secret'
        ];

        $index = 0;
        $this->container
            ->expects($this->at($index++))
            ->method('get')
            ->with('Config\Github.Auth')
            ->willReturn($config);

        $this->container
            ->expects($this->at($index++))
            ->method('get')
            ->with('Http\Request')
            ->willReturn($this->request);

        $this->container
            ->expects($this->at($index++))
            ->method('get')
            ->with('SessionManager')
            ->willReturn($this->sessionManager);

        $service = (new AuthServiceFactory())($this->container);
        $this->assertInstanceOf(AuthService::class, $service);
    }
}
