<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Exception\Configuration\InvalidConfigurationException;

try
{
    return RectorConfig::configure()
        ->withPaths(paths: [
            __DIR__ . '/src',
            __DIR__ . '/config',
        ])
        ->withPreparedSets(
            deadCode        : true,
            codeQuality     : true,
            typeDeclarations: true,
            privatization   : true,
            earlyReturn     : true,
            strictBooleans  : true,
        )
        ->withPhpSets(php84: true);
}
catch (InvalidConfigurationException)
{
}
