<?php

namespace Common\Read;

use Broadway\Serializer\SimpleInterfaceSerializer;

abstract class SerializableRepository
{
    protected $class;
    protected $serializer;

    public function __construct(string $class)
    {
        $this->class = $class;
        $this->serializer = $this->getSerializer();
    }

    protected function getSerializer()
    {
        return new SimpleInterfaceSerializer();
    }

    protected function deserializeItem($item)
    {
        return $this->serializer->deserialize(['class' => $this->class, 'payload' => $item]);
    }

    protected function deserializeItems($data)
    {
        return array_map([$this, 'deserializeItem'], $data);
    }
}
