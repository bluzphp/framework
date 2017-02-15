<?php
/**
 * Cache configuration
 *
 * @link https://github.com/bluzphp/framework/wiki/Cache
 * @return array
 */
return array(
    "enabled" => false,
    "adapter" => "filesystem",
    "pools" => array(
        /**
         * @link https://github.com/php-cache/filesystem-adapter
         */
        "filesystem" => function() {
            $filesystemAdapter = new \League\Flysystem\Adapter\Local(PATH_ROOT . '/tests/cache');
            $filesystem        = new \League\Flysystem\Filesystem($filesystemAdapter);

            return new \Cache\Adapter\Filesystem\FilesystemCachePool($filesystem);
        }
    )
);
