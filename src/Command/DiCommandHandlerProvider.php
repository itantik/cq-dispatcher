<?php

declare(strict_types=1);

namespace Itantik\CQDispatcher\Command;

use Itantik\CQDispatcher\DI\DiHandlerProvider;
use Itantik\CQDispatcher\DI\IContainer;
use Itantik\CQDispatcher\Exceptions\HandlerNotFoundException;
use Itantik\Middleware\IRequest;

class DiCommandHandlerProvider implements ICommandHandlerProvider
{
    /** @var DiHandlerProvider */
    private $handlerProvider;


    public function __construct(IContainer $container)
    {
        $handlerProvider = new DiHandlerProvider($container);
        $handlerProvider->setRequestSuffix('Command');
        $this->handlerProvider = $handlerProvider;
    }

    /**
     * @param IRequest $command
     * @return ICommandHandler
     * @throws HandlerNotFoundException
     */
    public function createHandler(IRequest $command): ICommandHandler
    {
        $handler = $this->handlerProvider->createHandler($command);
        if (!($handler instanceof ICommandHandler)) {
            throw new HandlerNotFoundException(
                sprintf("Command handler '%s' must implement '%s'.", get_class($handler), ICommandHandler::class)
            );
        }

        return $handler;
    }

    /**
     * Mandatory handler suffix.
     * @param string $handlerSuffix
     */
    public function setHandlerSuffix(string $handlerSuffix): void
    {
        $this->handlerProvider->setHandlerSuffix($handlerSuffix);
    }

    /**
     * Optional command suffix. Valid class names: SomeCommand, Some.
     * @param string $commandSuffix
     */
    public function setCommandSuffix(string $commandSuffix): void
    {
        $this->handlerProvider->setRequestSuffix($commandSuffix);
    }
}
