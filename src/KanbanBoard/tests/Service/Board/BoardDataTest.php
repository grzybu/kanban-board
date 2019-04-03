<?php

namespace KanbanBoard\Service\Board;

use function Clue\StreamFilter\fun;
use Github\Api\Issue\Milestones;
use KanbanBoard\Read\Repository\Model;
use KanbanBoard\Read\Repository\Repository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BoardDataTest extends TestCase
{

    /** @var MockObject */
    protected $repositories;

    /** @var MockObject */
    protected $milestones;

    /**
     * @var MockObject
     */
    protected $issues;

    protected $config;


    public function setUp()
    {
        parent::setUp();

        $this->config = ['account' => 'tester', 'pausedLabels' => ['test']];

        $this->repositories = $this->getMockBuilder(Repository::class)->disableOriginalConstructor()->getMock();
        $this->milestones = $this->getMockBuilder(\KanbanBoard\Read\Milestone\Repository::class)->disableOriginalConstructor()->getMock();
        $this->issues = $this->getMockBuilder(\KanbanBoard\Read\Issue\Repository::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * @test
     */
    public function itCanGetMilestones()
    {

        $repositoryModel = new Model('test-repo');
        $this->repositories->expects($this->at(0))
            ->method('getAll')
            ->willReturn([
                $repositoryModel
            ]);

        $this->milestones->expects($this->at(0))
            ->method('getMilestones')
            ->with($this->config['account'], $repositoryModel->getId())
            ->willReturn($this->getSampleMilestones());

        $this->issues->expects($this->any())
            ->method('getIssues')
            ->willReturn($this->getSampleIssues());

        $boardData = new BoardData($this->config, $this->repositories, $this->milestones, $this->issues);

        $this->assertInternalType('array', $boardData->getMilestones());

    }

    protected function getSampleMilestones()
    {
        $milestones = [];
        for ($i = 0; $i < rand(3, 10); $i++) {

            $data = [
                'id' => 'id-' . $i,
                'title' => 'title-' . $i,
                'number' => $i,
                'closed_issues' => rand(0, 100),
                'open_issues' => rand(0, 100),
            ];
            $milestones[] = \KanbanBoard\Read\Milestone\Model::deserialize($data);
        }

        return $milestones;
    }


    protected function getSampleIssues()
    {
        $issues = [];
        $rand = rand(30,200);
        for ($i = 0; $i < $rand; $i++) {

            $states = ['open', 'closed', 'random'];

            $assignee = [
                'login' => 'login-' . $i,
            ];

            $assignee +=  $i%3 === 0 ? ['avatar_url' => 'img.jpg'] : [];

            $data = [
                'id' => 'id-' . $i,
                'title' => 'issue-' . $i,
                'body' => str_repeat('[ ] ', rand(0, 20)) . str_repeat('[ x ]', rand(0, 20)),
                'number' => $i,
                'state' => $states[array_rand($states)],
                'pull_request' => $i % rand(1, 3) === 0 ? 'pull_request' : null,
                'assignee' => $i % 2 === 0 ? $assignee : null,
            ];
            $issues[] = \KanbanBoard\Read\Issue\Model::deserialize($data);
        }

        return $issues;
    }
}
