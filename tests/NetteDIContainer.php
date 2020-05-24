<?php

declare(strict_types=1);

namespace Tests;

use Itantik\CQDispatcher\DI\IContainer;
use Nette\DI\Container;

class NetteDIContainer implements IContainer
{
    /** @var Container */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function get(string $class)
    {
        return $this->container->createInstance($class);
    }
}
