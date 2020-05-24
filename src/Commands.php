<?php

declare(strict_types=1);

namespace Itantik\CQDispatcher;

use Itantik\CQDispatcher\Command\ICommandDispatcher;
use Itantik\Middleware\IMiddleware;
use Itantik\Middleware\IRequest;
use Itantik\Middleware\Manager;

abstract class Commands
{
    /** @var ICommandDispatcher */
    private $commandDispatcher;
    /** @var Manager */
    private $mwManager;


    public function __construct(ICommandDispatcher $commandDispatcher)
    {
        $this->commandDispatcher = $commandDispatcher;
        $this->mwManager = new Manager();
    }

    public function execute(IRequest $command): void
    {
        $this->commandDispatcher->dispatch($command, $this->mwManager);
    }

    protected function appendMiddleware(IMiddleware $middleware): void
    {
        $this->mwManager->append($middleware);
    }

    protected function prependMiddleware(IMiddleware $middleware): void
    {
        $this->mwManager->prepend($middleware);
    }

    protected function clearMiddlewares(): void
    {
        $this->mwManager->clear();
    }
}
