<?php

declare(strict_types=1);

namespace Itantik\CQDispatcher\Query;

use Itantik\CQDispatcher\DI\DiHandlerProvider;
use Itantik\CQDispatcher\DI\IContainer;
use Itantik\CQDispatcher\Exceptions\HandlerNotFoundException;
use Itantik\Middleware\IRequest;

class DiQueryHandlerProvider implements IQueryHandlerProvider
{
    /** @var DiHandlerProvider */
    private $handlerProvider;


    public function __construct(IContainer $container)
    {
        $handlerProvider = new DiHandlerProvider($container);
        $handlerProvider->setRequestSuffix('Query');
        $this->handlerProvider = $handlerProvider;
    }

    /**
     * @param IRequest $query
     * @return IQueryHandler
     * @throws HandlerNotFoundException
     */
    public function createHandler(IRequest $query): IQueryHandler
    {
        $handler = $this->handlerProvider->createHandler($query);
        if (!($handler instanceof IQueryHandler)) {
            throw new HandlerNotFoundException(
                sprintf("Query handler '%s' must implement '%s'.", get_class($handler), IQueryHandler::class)
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
     * Optional query suffix. Valid class names: SomeQuery, Some.
     * @param string $querySuffix
     */
    public function setQuerySuffix(string $querySuffix): void
    {
        $this->handlerProvider->setRequestSuffix($querySuffix);
    }
}
