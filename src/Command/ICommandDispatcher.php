<?php

declare(strict_types=1);

namespace Itantik\CQDispatcher\Command;

use Itantik\Middleware\IRequest;
use Itantik\Middleware\Manager;

interface ICommandDispatcher
{
    public function dispatch(IRequest $command, ?Manager $middlewareManager): void;
}
