<?php
/**
 * Created by PhpStorm.
 * User: grzybu
 * Date: 2019-04-01
 * Time: 20:57
 */

namespace KanbanBoard\Read\Repository;

use PHPUnit\Framework\TestCase;

class ModelTest extends TestCase
{

    /**
     * @test
     */
    public function testGetId()
    {
        $model = new Model('TEST');
        $this->assertEquals('TEST', $model->getId());
    }
}
