<?php

namespace KanbanBoard\Service\Github;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use KanbanBoard\Service\Auth\AuthService;
use Github\Client as GithubClient;

class GithubTest extends TestCase
{

    protected $githubClient;

    /**
     * @var MockObject
     */
    protected $authService;

    public function setUp()
    {
        parent::setUp();
        $this->githubClient = $this->getMockBuilder(GithubClient::class)->disableOriginalConstructor()->getMock();
        $this->authService = $this->getMockBuilder(AuthService::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * @test
     */
    public function testGetClient()
    {
        $this->authService
            ->expects($this->at(0))
            ->method('isAuthenticated')
            ->willReturn(true);

        $service = new Github($this->githubClient, $this->authService);
        $this->assertInstanceOf(GithubClient::class, $service->getClient());
    }

    /**
     * @test
     */
    public function testItThrowsException()
    {
        $this->authService
            ->expects($this->at(0))
            ->method('isAuthenticated')
            ->willReturn(false);

        $this->expectException(\RuntimeException::class);
        $service = new Github($this->githubClient, $this->authService);
        $service->getClient();
    }
}
