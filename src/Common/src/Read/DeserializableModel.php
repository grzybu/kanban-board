<?php

declare(strict_types=1);

namespace Common\Read;

interface DeserializableModel
{
    /**
     * @param array $data
     * @return The object insances
     */
    public static function deserialize(array $data);
}
