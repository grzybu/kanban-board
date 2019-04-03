<?php

namespace KanbanBoard\Read\Issue;

use Broadway\ReadModel\SerializableReadModel;
use Broadway\ReadModel\Testing\SerializableReadModelTestCase;
use Common\Read\DeserializableModel;
use Common\Read\DeserializableReadModelTestCase;
use Common\Read\Deserializer;

class ModelTest extends DeserializableReadModelTestCase
{

    protected function getSerializedData(): array
    {
        return [
            'class' => Model::class,
            'payload' => [
                'id' => 1,
                'title' => 'Test',
                'number' => 1,
                'assignee' => [
                    'login' => 'test-login',
                    'avatar_url' => 'avatar.jpg',
                    'other' => 'other-value'
                ],
                'labels' => [['name' => 'test-1']]
            ],
        ];
    }

    protected function getModel(): Model
    {
        $model = new Model(1);
        $model->setLabels([['name' => 'test-1']]);
        return $model;
    }

    /**
     * @test
     */
    public function testGetId()
    {
        $model = new Model('id-1');
        $this->assertEquals('id-1', $model->getId());
    }

    /**
     * @test
     */
    public function itCanSetAndGetProperties()
    {
        $model = new Model(1);

        $assignee = ['login' => 'test-login', 'avatar' => 'image.jpg'];
        $model->setAssignee($assignee);
        $this->assertEquals($assignee, $model->getAssignee());

        $this->assertEquals('image.jpg?s=16', $model->getAssigneeAvatar());
        $model->setAssignee(null);
        $this->assertEquals(null, $model->getAssigneeAvatar());

        $closedAt  = time();
        $model->setClosedAt($closedAt);
        $this->assertEquals($closedAt, $model->getClosedAt());
        $this->assertEquals(true, $model->isClosed());


        $model->setHtmlUrl('http://');
        $this->assertEquals($model->getHtmlUrl(), 'http://');

        $model->setNumber(1);
        $this->assertEquals(1, $model->getNumber());

        $model->setPullRequest('pull-1');
        $this->assertEquals(true, $model->isPullRequest());


        $model->setTitle('title-1');
        $this->assertEquals('title-1', $model->getTitle());

    }


    /**
     * @test
     */
    public function itCanGetState()
    {
        $model = new Model(1);
        $model->setState('closed');

        $this->assertEquals('closed', $model->getState());

    }



    public function itCanGetPercentage()
    {
        $model = new Model(1);

        $model->setOpenIssues(1);
        $model->setClosedIssues(2);

        $expected = [
            'total' => 3,
            'complete' => 2,
            'remaining' => 1,
            'percent' => 67.
        ];

        $this->assertEquals($expected, $model->getPercent());
    }
    
    public function itReturnsEmptyPercentageWhenTotalZero()
    {
        $model = new Model(1);

        $model->setOpenIssues(0);
        $model->setClosedIssues(0);

        $expected = [];

        $this->assertEquals($expected, $model->getPercent());
    }

    /**
     * @test
     */
    public function itCanDeserializeWithoutAssignee()
    {
        $data = $this->getSerializedData();
        unset($data['payload']['assignee']['login']);

        $deserializer = new Deserializer();
        $deserializedModel = $deserializer->deserialize($data);

        $this->assertInstanceOf(Model::class, $deserializedModel);

    }

    /**
     * @test
     */
    public function itHasLabels()
    {
        $model = $this->getModel();
        $this->assertEquals([], $model->hasLabel('test'));
        $this->assertEquals(['test-1'], $model->hasLabel(['test-1']));
    }

    /**
     * @test
     */
    public function itCanReturnBoardState()
    {
        $model = new Model(1);
        $model->setState('closed');
        $this->assertEquals('completed', $model->getBoardState());

        $model->setState('new');
        $model->setAssignee(['login' => 'tester']);
        $this->assertEquals('active', $model->getBoardState());

        $model->setAssignee(null);
        $this->assertEquals('queued', $model->getBoardState());
    }

    /**
     * @test
     */
    public function itCanReturnProgress()
    {
        $model = new Model(1);
        $model->setBody('[ ] [x] [ ] [ x ]');

        $this->assertInternalType('array', $model->getProgress());

    }
}
