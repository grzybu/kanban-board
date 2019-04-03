<?php

namespace Common\Session;

use PHPUnit\Framework\TestCase;

class SessionManagerTest extends TestCase
{


    private function getSessionManager():  SessionManager
    {
        return new SessionManager('KNBSESSIONID');
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function testIsExpired()
    {
        $sessionManager = $this->getSessionManager();

        $this->assertEquals(false, $sessionManager->isExpired());

        $_SESSION['_last_activity'] = time() - 356 * 60 *60;

        $this->assertEquals(true, $sessionManager->isExpired());
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function testIsValid()
    {
        $sessionManager = $this->getSessionManager();

        $this->assertEquals(true, $sessionManager->isValid());
    }


    /**
     * @test
     * @runInSeparateProcess
     */
    public function testItCanStart()
    {
        $sessionManager = $this->getSessionManager();
        $this->assertEquals(true, $sessionManager->start());
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function testItCanStartOnce()
    {
        $sessionManager = $this->getSessionManager();
        $this->assertEquals(true, $sessionManager->start());
        $this->assertEquals(false, $sessionManager->start());
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function testItCanPutAndGet()
    {
        $sessionManager = $this->getSessionManager();

        $sessionManager->put('a', 'value-1');
        $this->assertEquals('value-1', $sessionManager->get('a'));
        $this->assertEquals(null, $sessionManager->get('b'));

        $sessionManager->put('Class.A', 'value-1');
        $this->assertEquals('value-1', $sessionManager->get('Class.A'));
    }


    /**
     * @test
     * @runInSeparateProcess
     */
    public function testCanRefresh()
    {
        $sessionManager = $this->getSessionManager();
        $sessionManager->put('a', 'value-1');
        $this->assertEquals(true, $sessionManager->refresh());
        $this->assertEquals('value-1', $sessionManager->get('a'));
    }


    /**
     * @test
     * @runInSeparateProcess
     */
    public function testCanForget()
    {
        $sessionManager = $this->getSessionManager();
        $sessionManager->put('a', 'value-1');
        $this->assertEquals(true, $sessionManager->forget());
        $sessionManager = $this->getSessionManager();
        $this->assertEquals(false, $sessionManager->forget());
    }
}
