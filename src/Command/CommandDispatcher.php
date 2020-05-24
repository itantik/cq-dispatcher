<?php

declare(strict_types=1);

namespace Itantik\CQDispatcher\Command;

use Exception;
use Itantik\Middleware\IRequest;
use Itantik\Middleware\Manager;
use Itantik\CQDispatcher\Middleware\CoreCommandLayer;

class CommandDispatcher implements ICommandDispatcher
{
    /** @var ICommandHandlerProvider */
    private $handlerProvider;


    public function __construct(ICommandHandlerProvider $handlerProvider)
    {
        $this->handlerProvider = $handlerProvider;
    }

    /**
     * @param IRequest $command
     * @param Manager|null $middlewareManager
     * @return void
     * @throws Exception
     */
    public function dispatch(IRequest $command, ?Manager $middlewareManager): void
    {
        $handler = $this->handlerProvider->createHandler($command);

        if ($middlewareManager && !$middlewareManager->isEmpty()) {
            $coreLayer = new CoreCommandLayer($handler);
            $middlewareManager->process($command, $coreLayer);
        } else {
            $handler->handle($command);
        }
    }
}
