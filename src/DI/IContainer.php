<?php

declare(strict_types=1);

namespace Itantik\CQDispatcher\DI;

use Exception;

interface IContainer
{
    /**
     * Gets class instance.
     * @param string $class
     * @return object
     * @throws Exception
     */
    public function get(string $class);
}
