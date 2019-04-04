<?php

namespace KanbanBoard\Service\Auth;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Common\Session\SessionManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

class AuthServiceTest extends TestCase
{
    protected $config;

    protected $httpRequest;

    /** @var MockObject */
    protected $httpClient;

    /** @var MockObject */
    protected $sessionManager;

    protected $session = [];


    public function setUp()
    {
        parent::setUp();

        $this->config = [
            'clientId' => 'TEST-1',
            'clientSecret' => 'TEST-Secret'
        ];

        $this->httpClient = $this->getMockBuilder(Client::class)->disableOriginalConstructor()->getMock();
        $this->httpRequest = $this->getMockBuilder(\Symfony\Component\HttpFoundation\Request::class)->disableOriginalConstructor()->getMock();
        $this->sessionManager = $this->getMockBuilder(SessionManager::class)->disableOriginalConstructor()->getMock();
        $_SESSION = $this->session;
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function testItReturnsToken()
    {
        $token = 'TOKEN';
        $this->sessionManager->expects($this->at(0))
            ->method('get')
            ->with('gh-token')
            ->willReturn($token);

        $sevice = new AuthService($this->config, $this->httpClient, $this->httpRequest, $this->sessionManager);

        $this->assertEquals($sevice->getToken(), $token);
    }

    /**
     * @test
     */
    public function testItCanReturnAuthenticatedWhenTokenSet()
    {
        $token = 'test';

        $this->sessionManager->expects($this->at(0))
            ->method('get')
            ->with('gh-token')
            ->willReturn($token);

        $sevice = new AuthService($this->config, $this->httpClient, $this->httpRequest, $this->sessionManager);
        $this->assertEquals($sevice->isAuthenticated(), true);
    }

    /**
     * @test
     */
    public function itCanReturnNotAuthenticate()
    {
        $sevice = new AuthService($this->config, $this->httpClient, $this->httpRequest, $this->sessionManager);
        $this->assertEquals($sevice->isAuthenticated(), false);
    }


    /**
     * @test
     */
    public function itReturnsWhenAuthenticated()
    {
        $token = 'TOKEN';
        $this->sessionManager->expects($this->at(0))
            ->method('get')
            ->with('gh-token')
            ->willReturn($token);

        $sevice = new AuthService($this->config, $this->httpClient, $this->httpRequest, $this->sessionManager);


        $this->assertEquals($sevice->login('code', 'state'), true);
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function itCanRequestAuthentity()
    {

        $service = new AuthService($this->config, $this->httpClient, $this->httpRequest, $this->sessionManager);

        $service->requestIdentity();
        $locationUrl = "Location: https://github.com/login/oauth/authorize?";

        $hasGithubRedirect = false;

        foreach (xdebug_get_headers() as $header) {
            if (strpos($header, $locationUrl) === 0) {
                $hasGithubRedirect = true;
            }
        }

        $this->assertEquals(true, $hasGithubRedirect);
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function itCanCreateStateParamAndRequestIdentity()
    {
        $sevice = new AuthService($this->config, $this->httpClient, $this->httpRequest, $this->sessionManager);
        $sevice->requestIdentity();

        $xdebug_params = xdebug_get_headers();

        $redirects = false;

        array_walk($xdebug_params, function ($value) use (&$redirects) {
            if (strpos($value, 'Location') === 0) {
                $redirects = true;
            }
        });

        $this->assertEquals(true, $redirects);
    }

    /**
     * @test
     */
    public function itCanLogin()
    {
        $state = random_bytes(32);
        $code = 'test-code';

        $postData = [
            'client_id' => $this->config['clientId'],
            'client_secret' => $this->config['clientSecret'],
            'state' => $state,
            'code' => $code
        ];

        $this->sessionManager->expects($this->at(0))
            ->method('get')
            ->with('gh-token')
            ->willReturn(null);

        $this->sessionManager->expects($this->at(1))
            ->method('get')
            ->with('state')
            ->willReturn($state);

        $response = $this->getMockBuilder(Response::class)->disableOriginalConstructor()->getMock();
        $streamInterface = $this->getMockBuilder(StreamInterface::class)->disableOriginalConstructor()->getMock();
        $contents = json_encode(['access_token' => 'test-token']);

        $streamInterface->expects($this->at(0))
            ->method('getContents')
            ->willReturn($contents);

        $response->expects($this->at(0))
            ->method('getBody')
            ->willReturn($streamInterface);

        $this->httpClient->expects($this->at(0))
            ->method('request')
            ->with(
                'POST',
                'https://github.com/login/oauth/access_token',
                [
                    'query' => http_build_query($postData),
                    'headers' => [
                        'Accept' => 'application/json'
                    ]
                ]
            )
            ->willReturn($response);

        $sevice = new AuthService($this->config, $this->httpClient, $this->httpRequest, $this->sessionManager);
        $this->assertEquals($sevice->login($code, $state), true);
    }

    /**
     * @test
     */
    public function itThrowsExceptionWhenWrongResponse()
    {
        $code = 'test-code';
        $state = 'test';

        $index = 0;
        $this->sessionManager->expects($this->at($index++))
            ->method('get')
            ->with('gh-token')
            ->willReturn(null);

        $this->sessionManager->expects($this->at($index++))
            ->method('get')
            ->with('state')
            ->willReturn($state);


        $postData = [
            'client_id' => $this->config['clientId'],
            'client_secret' => $this->config['clientSecret'],
            'state' => $state,
            'code' => $code
        ];

        $response = $this->getMockBuilder(Response::class)->disableOriginalConstructor()->getMock();
        $streamInterface = $this->getMockBuilder(StreamInterface::class)->disableOriginalConstructor()->getMock();
        $contents = json_encode(['mallformed-return' => 'dd']);

        $streamInterface->expects($this->at(0))
            ->method('getContents')
            ->willReturn($contents);

        $response->expects($this->at(0))
            ->method('getBody')
            ->willReturn($streamInterface);

        $this->httpClient->expects($this->at(0))
            ->method('request')
            ->with(
                'POST',
                'https://github.com/login/oauth/access_token',
                [
                    'query' => http_build_query($postData),
                    'headers' => [
                        'Accept' => 'application/json'
                    ]
                ]
            )
            ->willReturn($response);

        $sevice = new AuthService($this->config, $this->httpClient, $this->httpRequest, $this->sessionManager);
        $this->expectException(\RuntimeException::class);
        $sevice->login($code, $state);
    }

    /**
     * @test
     */
    public function itThrowsExceptionWhenErrorResponse()
    {
        $code = 'test-code';
        $state = 'test';

        $index = 0;
        $this->sessionManager->expects($this->at($index++))
            ->method('get')
            ->with('gh-token')
            ->willReturn(null);

        $this->sessionManager->expects($this->at($index++))
            ->method('get')
            ->with('state')
            ->willReturn($state);


        $postData = [
            'client_id' => $this->config['clientId'],
            'client_secret' => $this->config['clientSecret'],
            'state' => $state,
            'code' => $code
        ];

        $response = $this->getMockBuilder(Response::class)->disableOriginalConstructor()->getMock();
        $streamInterface = $this->getMockBuilder(StreamInterface::class)->disableOriginalConstructor()->getMock();
        $contents = json_encode(['error' => 'Unknown error']);

        $streamInterface->expects($this->at(0))
            ->method('getContents')
            ->willReturn($contents);

        $response->expects($this->at(0))
            ->method('getBody')
            ->willReturn($streamInterface);

        $this->httpClient->expects($this->at(0))
            ->method('request')
            ->with(
                'POST',
                'https://github.com/login/oauth/access_token',
                [
                    'query' => http_build_query($postData),
                    'headers' => [
                        'Accept' => 'application/json'
                    ]
                ]
            )
            ->willReturn($response);

        $sevice = new AuthService($this->config, $this->httpClient, $this->httpRequest, $this->sessionManager);
        $this->expectException(\RuntimeException::class);
        $sevice->login($code, $state);
    }

    /**
     * @test
     */
    public function itCannotLoginWithWrongStateParam()
    {
        $state = random_bytes(32);
        $code = 'test-code';

        $this->sessionManager->expects($this->at(0))
            ->method('get')
            ->with('gh-token')
            ->willReturn(null);

        $this->sessionManager->expects($this->at(1))
            ->method('get')
            ->with('state')
            ->willReturn($state);

        $sevice = new AuthService($this->config, $this->httpClient, $this->httpRequest, $this->sessionManager);
        $this->expectException(\RuntimeException::class);
        $sevice->login($code, $state . 'xx');
    }
}
