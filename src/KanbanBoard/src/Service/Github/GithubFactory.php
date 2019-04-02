<?php

namespace KanbanBoard\Service\Github;

use Cache\Adapter\Filesystem\FilesystemCachePool;
use Psr\Container\ContainerInterface;
use Psr\SimpleCache\CacheInterface;
use Github\Client as GithubClient;

class GithubFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $cachePool = $this->getCachePool();
        $client = $this->getClient($cachePool);
        $authService = $container->get('Service\Auth');

        return new Github($client, $authService);
    }

    protected function getClient(CacheInterface $cachePool)
    {
        $client = new GithubClient();
        $client->addCache($cachePool);

        return new $client;
    }

    protected function getCachePool(): CacheInterface
    {
        $filesystemAdapter = new \League\Flysystem\Adapter\Local(sys_get_temp_dir() . '/github-api-cache');
        $filesystem = new \League\Flysystem\Filesystem($filesystemAdapter);
        return new \Cache\Adapter\Filesystem\FilesystemCachePool($filesystem);
    }
}
