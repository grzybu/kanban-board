<?php

declare(strict_types=1);

namespace KanbanBoard\Controller\Board;

use KanbanBoard\Service\Auth\AuthService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use KanbanBoard\Read\Repository\Repository as RepositoryRepository;

class BoardControllerTest extends TestCase
{

    /** @var MockObject */
    private $authService;

    /** @var MockObject */
    private $mustacheEngine;

    /** @var MockObject */
    private $repositoryRepo;

    /** @var MockObject */
    private $response;

    public function setUp()
    {
        parent::setUp();
        $this->authService = $this->getMockBuilder(AuthService::class)->disableOriginalConstructor()->getMock();
        $this->mustacheEngine = $this->getMockBuilder(\Mustache_Engine::class)->disableOriginalConstructor()->getMock();
        $this->repositoryRepo = $this->getMockBuilder(RepositoryRepository::class)->disableOriginalConstructor()->getMock();

        $this->response = $this->getMockBuilder(Response::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * @test
     */
    public function testItReturnResponse()
    {
        $contoller = new BoardController($this->authService, $this->mustacheEngine, $this->repositoryRepo, $this->response);

        $templateName = 'index';
        $data = [0 => ['milestone' => 'ver1', 'url' => 'https://github.com/grzybu/angular-charts/milestone/1', 'progress' => ['total' => 3, 'complete' => 1, 'remaining' => 2, 'percent' => 33.0,], 'queued' => [0 => ['id' => 425993764, 'number' => 2, 'title' => 'pagination doesn\'t work', 'url' => 'https://github.com/grzybu/angular-charts/issues/2', 'assignee' => null, 'paused' => [], 'progress' => [], 'closed' => null,],], 'active' => [0 => ['id' => 426142186, 'number' => 5, 'title' => 'test active issues', 'url' => 'https://github.com/grzybu/angular-charts/issues/5', 'assignee' => 'https://avatars2.githubusercontent.com/u/2069219?v=4?s=16', 'paused' => [], 'progress' => [], 'closed' => null,],], 'completed' => [0 => ['id' => 425994317, 'number' => 3, 'title' => 'create e2e tests', 'url' => 'https://github.com/grzybu/angular-charts/issues/3', 'assignee' => null, 'paused' => [], 'progress' => [], 'closed' => '2019-03-27T19:22:08Z',],],], 1 => ['milestone' => 'ver2', 'url' => 'https://github.com/grzybu/angular-charts/milestone/2', 'progress' => ['total' => 2, 'complete' => 0, 'remaining' => 2, 'percent' => 0.0,], 'queued' => [0 => ['id' => 425993434, 'number' => 1, 'title' => 'Create tests', 'url' => 'https://github.com/grzybu/angular-charts/issues/1', 'assignee' => null, 'paused' => [], 'progress' => [], 'closed' => null,],], 'active' => [0 => ['id' => 426141671, 'number' => 4, 'title' => 'tests', 'url' => 'https://github.com/grzybu/angular-charts/issues/4', 'assignee' => 'https://avatars2.githubusercontent.com/u/2069219?v=4?s=16', 'paused' => [], 'progress' => [], 'closed' => null,],], 'completed' => [],],];
        $contentet = '<html>TEST</html>';

        $this->authService->expects($this->at(0))
            ->method('isAuthenticated')
            ->willReturn(true);

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
        $contoller = new BoardController($this->authService, $this->mustacheEngine, $this->repositoryRepo, $this->response);

        $this->authService->expects($this->at(0))
            ->method('isAuthenticated')
            ->willReturn(false);

        $response = new RedirectResponse('/auth');

        $this->assertEquals($response, call_user_func($contoller));
    }
}
