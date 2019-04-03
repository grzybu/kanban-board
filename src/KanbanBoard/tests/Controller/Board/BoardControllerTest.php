<?php

declare(strict_types=1);

namespace KanbanBoard\Controller\Board;

use KanbanBoard\Service\Auth\AuthService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use KanbanBoard\Service\Board\BoardData;

class BoardControllerTest extends TestCase
{

    /** @var MockObject */
    private $authService;

    /** @var MockObject */
    private $mustacheEngine;

    /** @var MockObject */
    private $boarDataService;

    /** @var MockObject */
    private $response;

    public function setUp()
    {
        parent::setUp();
        $this->authService = $this->getMockBuilder(AuthService::class)->disableOriginalConstructor()->getMock();
        $this->mustacheEngine = $this->getMockBuilder(\Mustache_Engine::class)->disableOriginalConstructor()->getMock();
        $this->boarDataService = $this->getMockBuilder(BoardData::class)->disableOriginalConstructor()->getMock();
        $this->response = $this->getMockBuilder(Response::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * @test
     */
    public function testItReturnResponse()
    {
        $contoller = new BoardController($this->authService, $this->mustacheEngine, $this->boarDataService, $this->response);

        $templateName = 'index';

        $data = [];

        $contentet = '<html>TEST</html>';

        $this->authService->expects($this->at(0))
            ->method('isAuthenticated')
            ->willReturn(true);

        $this->boarDataService->expects($this->at(0))
            ->method('getMilestones')
            ->willReturn($data);

        $this->mustacheEngine->expects($this->at(0))
            ->method('render')
            ->with($templateName, ['milestones' => $data])
            ->willReturn($contentet);

        $this->response->expects($this->at(0))
            ->method('setContent')
            ->with($contentet)
            ->willReturnSelf();


        $this->assertEquals(call_user_func($contoller), $this->response);
    }

    /**
     * @test
     */
    public function testItRedirectsToLogin()
    {
        $contoller = new BoardController($this->authService, $this->mustacheEngine, $this->boarDataService, $this->response);

        $this->authService->expects($this->at(0))
            ->method('isAuthenticated')
            ->willReturn(false);

        $response = new RedirectResponse('/auth');

        $this->assertEquals($response, call_user_func($contoller));
    }
}
