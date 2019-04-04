<?php

declare(strict_types=1);

namespace KanbanBoard\Controller\Auth;

use KanbanBoard\Service\Auth\StateVerifyException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use KanbanBoard\Service\Auth\AuthService;

class AuthControllerTest extends TestCase
{

    /** @var MockObject */
    private $authService;

    /** @var MockObject */
    private $request;

    /** @var MockObject */
    private $response;

    public function setUp()
    {
        parent::setUp();
        $this->authService = $this->getMockBuilder(AuthService::class)->disableOriginalConstructor()->getMock();
        $this->request = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();
        $this->response = $this->getMockBuilder(Response::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * @test
     */
    public function testItCanAuthorizeAndReturn()
    {
        $code = 'test';
        $state = 'state';

        $this->authService->expects($this->at(0))
            ->method('isAuthenticated')
            ->willReturn(false);

        $this->request->expects($this->at(0))
            ->method('get')
            ->with('code')
            ->willReturn($code);

        $this->request->expects($this->at(1))
            ->method('get')
            ->with('state')
            ->willReturn($state);

        $this->authService->expects($this->at(1))
            ->method('login')
            ->with($code, $state)
            ->willReturn(true);

        $response = new RedirectResponse('/');

        $contoller = new AuthController($this->authService, $this->request, $this->response);
        $this->assertEquals(call_user_func($contoller), $response);
    }

    /**
     * @test
     */
    public function testItCanReturnWhenAuthorized()
    {
        $response = new RedirectResponse('/');

        $this->authService->expects($this->at(0))
            ->method('isAuthenticated')
            ->willReturn(true);

        $contoller = new AuthController($this->authService, $this->request, $this->response);
        $this->assertEquals(call_user_func($contoller), $response);
    }

    /**
     * @test
     */
    public function testItCanHandleException()
    {
        $exceptionMsg = 'Test';

        $code = 'code';
        $state = 'state';

        $this->request->expects($this->at(0))
            ->method('get')
            ->with('code')
            ->willReturn($code);

        $this->request->expects($this->at(1))
            ->method('get')
            ->with('state')
            ->willReturn($state);

        $index = 0;
        $this->authService->expects($this->at($index++))
            ->method('isAuthenticated')
            ->willReturn(false);

        $this->authService->expects($this->at($index++))
            ->method('login')
            ->with($code, $state)
            ->willThrowException(new \RuntimeException($exceptionMsg));

        $index = 0;
        $this->response->expects($this->at($index++))
            ->method('setStatusCode')
            ->withAnyParameters(403)
            ->willReturnSelf();

        $this->response->expects($this->at($index++))
            ->method('setContent')
            ->withAnyParameters($exceptionMsg)
            ->willReturnSelf();

        $contoller = new AuthController($this->authService, $this->request, $this->response);

        $this->assertEquals(call_user_func($contoller), $this->response);
    }

    /**
     * @test
     */
    public function testItCanHandleStateVerifyException()
    {
        $exceptionMsg = 'Test';

        $code = 'code';
        $state = 'state';

        $this->request->expects($this->at(0))
            ->method('get')
            ->with('code')
            ->willReturn($code);

        $this->request->expects($this->at(1))
            ->method('get')
            ->with('state')
            ->willReturn($state);

        $index = 0;
        $this->authService->expects($this->at($index++))
            ->method('isAuthenticated')
            ->willReturn(false);

        $this->authService->expects($this->at($index++))
            ->method('login')
            ->with($code, $state)
            ->willThrowException(new StateVerifyException($exceptionMsg));

        $index = 0;
        $this->response->expects($this->at($index++))
            ->method('setStatusCode')
            ->withAnyParameters(403)
            ->willReturnSelf();

        $this->response->expects($this->at($index++))
            ->method('setContent')
            ->withAnyParameters($exceptionMsg)
            ->willReturnSelf();

        $contoller = new AuthController($this->authService, $this->request, $this->response);

        $this->assertEquals(call_user_func($contoller), $this->response);
    }


    /**
     * @runInSeparateProcess
     * @test
     */
    public function testItCanRequestIdenity()
    {
        $index = 0;
        $this->authService->expects($this->at($index++))
            ->method('isAuthenticated')
            ->willReturn(false);

        $this->authService->expects($this->at($index++))
            ->method('requestIdentity')
            ->willReturn(null);

        $contoller = new AuthController($this->authService, $this->request, $this->response);

        $this->assertEquals(call_user_func($contoller), null);
    }
}
