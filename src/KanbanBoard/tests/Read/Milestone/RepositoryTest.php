<?php

namespace KanbanBoard\Read\Milestone;

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
    public function testGetMilestones()
    {

        $account = 'test-account';
        $repositoryName = 'test-repo';

        $milestones = $this->getMockBuilder(Issue\Milestones::class)->disableOriginalConstructor()->getMock();

        $milestones->expects($this->at(0))
            ->method('all')
            ->with($account, $repositoryName)
            ->willReturn([]);

        $this->apiIssue->expects($this->at(0))
            ->method('milestones')
            ->willReturn($milestones);


        $this->githubClient->expects($this->at(0))
            ->method('api')
            ->with('issues')
            ->willReturn($this->apiIssue);

        $this->githubService->expects($this->at(0))
            ->method('getClient')
            ->willReturn($this->githubClient);

        $repository = new Repository($this->githubService, Model::class);
        $this->assertEquals([], $repository->getMilestones($account, $repositoryName));
    }
}
