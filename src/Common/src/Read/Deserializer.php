<?php

declare(strict_types=1);

namespace Common\Read;

class Deserializer
{
    public function deserialize(array $serializedObject)
    {

        if (!in_array(DeserializableModel::class, class_implements($serializedObject['class']))) {
            throw new \Exception(
                sprintf(
                    'Class \'%s\' does not implement Common\DeserializableModel',
                    $serializedObject['class']
                )
            );
        }

        return $serializedObject['class']::deserialize($serializedObject['payload']);
    }
}
