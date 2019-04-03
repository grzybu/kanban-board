<?php
/**
 * Created by PhpStorm.
 * User: grzybu
 * Date: 2019-04-01
 * Time: 20:57
 */

namespace KanbanBoard\Read\Milestone;

use Broadway\ReadModel\SerializableReadModel;
use Broadway\ReadModel\Testing\SerializableReadModelTestCase;
use Common\Read\DeserializableReadModelTestCase;

class ModelTest extends DeserializableReadModelTestCase
{

    protected function getSerializedData(): array
    {
        return [
            'class' => Model::class,
            'payload' => [
                'id' => 1,
                'title' => 'Test',
                'number' => 1
            ],
        ];
    }

    protected function getModel(): Model
    {
        $model = new Model(1);
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
        $repository = 'repo-1';
        $model = new Model(1);
        $model->setRepository($repository);
        $this->assertEquals($repository, $model->getRepository());

        $model->setClosedIssues(1);
        $this->assertEquals(1, $model->getClosedIssues());

        $model->setNumber(1);
        $this->assertEquals('1', $model->getNumber());

        $model->setOpenIssues(1);
        $this->assertEquals(1, $model->getOpenIssues());

        $model->setHtmlUrl('http://test.com');
        $this->assertEquals('http://test.com', $model->getHtmlUrl());

        $this->assertEquals($model->getUrl(), $model->getHtmlUrl());

        $model->setMilestone('milestone-1');
        $this->assertEquals($model->getMilestone(), 'milestone-1');

        $model->setTitle('title-1');
        $this->assertEquals('title-1', $model->getTitle());

        $model->setTitle('test-1');
        $this->assertEquals('test-1', $model->getTitle());
    }


    /**
     * @test
     */
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

    /**
     * @test
     */
    public function itReturnsEmptyPercentageWhenTotalZero()
    {
        $model = new Model(1);

        $model->setOpenIssues(0);
        $model->setClosedIssues(0);

        $expected = [];

        $this->assertEquals($expected, $model->getPercent());
    }
}
