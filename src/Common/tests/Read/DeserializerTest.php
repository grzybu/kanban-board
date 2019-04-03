<?php

namespace Common\Read;

use PHPUnit\Framework\TestCase;

class DeserializerTest extends TestCase
{
    /**
     * @test
     */
    public function itCanDeserialize()
    {
        $deserializer = new Deserializer();

        $identifier = 1;

        $object = new class($identifier) implements DeserializableModel
        {
            protected $identifier;

            public function __construct($identifier)
            {
                $this->identifier = $identifier;
            }

            public static function deserialize(array $data)
            {
                return new static($data['id']);
            }
        };

        $data = ['id' => $identifier];

        $this->assertEquals($deserializer->deserialize(['class' => get_class($object), 'payload' => $data]), $object);
    }

    /**
     * @test
     */
    public function itThrowsExecpiton()
    {
        $deserializer = new Deserializer();

        $object = new \stdClass();

        $data = [
            'class' => get_class($object),
            'payload' => ['id' => 'test']
        ];

        $this->expectException(\Exception::class);
        $deserializer->deserialize($data);
    }
}
