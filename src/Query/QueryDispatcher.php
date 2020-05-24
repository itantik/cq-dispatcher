<?php

declare(strict_types=1);

namespace Itantik\CQDispatcher\Query;

use Exception;
use Itantik\CQDispatcher\Middleware\CoreQueryLayer;
use Itantik\Middleware\IRequest;
use Itantik\Middleware\Manager;

class QueryDispatcher implements IQueryDispatcher
{
    /** @var IQueryHandlerProvider */
    private $handlerProvider;


    public function __construct(IQueryHandlerProvider $handlerProvider)
    {
        $this->handlerProvider = $handlerProvider;
    }

    /**
     * @param IRequest $query
     * @param Manager|null $middlewareManager
     * @return mixed
     * @throws Exception
     */
    public function dispatch(IRequest $query, ?Manager $middlewareManager)
    {
        $handler = $this->handlerProvider->createHandler($query);

        if ($middlewareManager && !$middlewareManager->isEmpty()) {
            $coreLayer = new CoreQueryLayer($handler);
            return $middlewareManager->process($query, $coreLayer);
        } else {
            return $handler->handle($query);
        }
    }
}
