<?php

declare(strict_types=1);

namespace Itantik\CQDispatcher\Command;

use Itantik\Middleware\IRequest;

interface ICommandHandlerProvider
{
    public function createHandler(IRequest $command): ICommandHandler;
}
