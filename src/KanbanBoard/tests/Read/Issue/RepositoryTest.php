<?php

namespace KanbanBoard\Read\Issue;

use Github\Api\Issue;
use Github\Client;
use KanbanBoard\Service\Github\Github as GithubService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RepositoryTest extends TestCase
{

    /**
     * @var MockObject
     */
    private $githubService;

    /**
     * @var MockObject
     */
    private $githubClient;

    /**
     * @var MockObject
     */
    private $apiIssue;

    public function setUp()
    {
        parent::setUp();
        $this->githubService = $this->getMockBuilder(GithubService::class)->disableOriginalConstructor()->getMock();
        $this->githubClient = $this->getMockBuilder(Client::class)->disableOriginalConstructor()->getMock();
        $this->apiIssue = $this->getMockBuilder(Issue::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * @test
     */
    public function testGetIssues()
    {

        $account = 'test-account';
        $repositoryName = 'test-repo';
        $milestone = 1;

        $this->githubService->expects($this->at(0))
            ->method('getClient')
            ->willReturn($this->githubClient);

        $this->githubClient->expects($this->at(0))
            ->method('api')
            ->with('issue')
            ->willReturn($this->apiIssue);

        $this->apiIssue->expects($this->at(0))
            ->method('all')
            ->with($account, $repositoryName, ['milestone' => $milestone, 'state' => 'all'])
            ->willReturn([]);



        $repository = new Repository($this->githubService, Model::class);
        $this->assertEquals([], $repository->getIssues($account, $repositoryName, $milestone));
    }
}
