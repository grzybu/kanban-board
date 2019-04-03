<?php

namespace Common\Read;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DeserializableRepositoryTest extends TestCase
{
    protected $class;
    /**
     * @var MockObject
     */
    protected $deserializer;

    public function setUp()
    {
        parent::setUp();
        $this->class = DeserializableTestModel::class;
        $this->deserializer = $this->getMockBuilder(Deserializer::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * @test
     */
    public function itCanDeserializeItem()
    {
        $repository = new DeserializableTestRepository(DeserializableTestModel::class);

        $model = new DeserializableTestModel(1, 'property-1');
        $this->assertEquals($model, $repository->getItem());
    }

    /**
     * @test
     */
    public function itCanDeserializeItems()
    {
        $repository = new DeserializableTestRepository(DeserializableTestModel::class);


        $data = [
            ['id' => 1, 'property' => 'property-1', 'test' => 3],
            ['id' => 2, 'property' => 'property-1', 'test' => 5]
        ];

        $models = [];
        foreach ($data as $item) {
            $models[] = new DeserializableTestModel($item['id'], $item['property']);
        }

        $this->assertEquals($models, $repository->getItems());
    }
}


class DeserializableTestModel implements DeserializableModel
{
    protected $id;
    protected $property;

    public function __construct($id, $property)
    {
        $this->id = $id;
        $this->property = $property;
    }

    public static function deserialize(array $data)
    {
        return new static($data['id'], $data['property']);
    }
}

class DeserializableTestRepository extends DeserializableRepository
{
    public function getItem()
    {
        return parent::deserializeItem(['id' => 1, 'property' => 'property-1', 'test' => 3]);
    }

    public function getItems()
    {
        return parent::deserializeItems([
            ['id' => 1, 'property' => 'property-1', 'test' => 3],
            ['id' => 2, 'property' => 'property-1', 'test' => 5]
        ]);
    }
}
