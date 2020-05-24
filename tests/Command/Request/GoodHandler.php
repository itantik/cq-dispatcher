<?php

declare(strict_types=1);

namespace Tests\Command\Request;

use Itantik\CQDispatcher\Command\ICommandHandler;
use Tests\Request\Loggable;

class GoodHandler implements ICommandHandler
{
    public function handle(GoodCommand $command)
    {
        if ($command instanceof Loggable) {
            $command->addLog('GoodHandler: handled');
        }
    }
}
