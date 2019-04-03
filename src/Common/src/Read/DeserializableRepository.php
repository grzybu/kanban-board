<?php

namespace Common\Read;

abstract class DeserializableRepository
{
    protected $class;
    protected $deserializer;

    public function __construct(string $class)
    {
        $this->class = $class;
        $this->deserializer = $this->getDeserializer();
    }

    protected function getDeserializer()
    {
        return new Deserializer();
    }

    protected function deserializeItem($item)
    {
        return $this->deserializer->deserialize(['class' => $this->class, 'payload' => $item]);
    }

    protected function deserializeItems($data)
    {
        return array_map([$this, 'deserializeItem'], $data);
    }
}
