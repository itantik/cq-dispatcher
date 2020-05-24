<?php

declare(strict_types=1);

namespace Tests\Command\Request;

use Itantik\CQDispatcher\Command\ICommandHandler;

class GoodRequestHandler implements ICommandHandler
{
    public function handle(GoodRequest $command)
    {
    }
}
