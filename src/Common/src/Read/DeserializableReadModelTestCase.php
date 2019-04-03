<?php

declare(strict_types=1);

namespace Common\Read;

use PHPUnit\Framework\TestCase;

abstract class DeserializableReadModelTestCase extends TestCase
{
    /**
     * @test
     */
    public function itsDeserializable()
    {
        $this->assertInstanceOf(DeserializableModel::class, $this->getModel());
    }

    /**
     * @test
     */
    public function itCanBeDeserialized()
    {
        $data = $this->getSerializedData();
        $deserializer = new Deserializer();
        $deserializedModel = $deserializer->deserialize($data);

        $this->assertInstanceOf(DeserializableModel::class, $deserializedModel);
    }

    abstract protected function getModel();

    abstract protected function getSerializedData() : array;
}
