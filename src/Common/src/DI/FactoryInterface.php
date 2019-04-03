<?php

namespace Common\DI;

use Psr\Container\ContainerInterface;

interface FactoryInterface
{
    public function __invoke(ContainerInterface $container);
}
