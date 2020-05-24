<?php

declare(strict_types=1);

namespace Tests;

use Nette\Configurator;

class Container
{
    public static function create(): \Nette\DI\Container
    {
        $configurator = new Configurator;

        $configurator->setDebugMode(false);

        $configurator->setTimeZone('Europe/Prague');

        $configurator->setTempDirectory(__DIR__ . '/temp');

        $configurator->createRobotLoader()
            ->addDirectory(__DIR__ . '/../src')
            ->addDirectory(__DIR__)
            ->register();

        return $configurator->createContainer();
    }
}
