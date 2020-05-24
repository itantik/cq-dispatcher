<?php

declare(strict_types=1);

namespace Itantik\CQDispatcher;

use Itantik\CQDispatcher\Middleware\DataResponse;
use Itantik\CQDispatcher\Middleware\DataTransportLayerFactory;
use Itantik\CQDispatcher\Query\IQueryDispatcher;
use Itantik\Middleware\IMiddleware;
use Itantik\Middleware\IRequest;
use Itantik\Middleware\Manager;

abstract class Queries
{
    /** @var IQueryDispatcher */
    private $queryDispatcher;
    /** @var Manager */
    private $mwManager;


    public function __construct(IQueryDispatcher $queryDispatcher)
    {
        $this->queryDispatcher = $queryDispatcher;
        $this->mwManager = new Manager(new DataTransportLayerFactory());
    }

    /**
     * @param IRequest $query
     * @return mixed
     */
    public function execute(IRequest $query)
    {
        $res = $this->queryDispatcher->dispatch($query, $this->mwManager);
        if ($res instanceof DataResponse) {
            /** @var DataResponse $res */
            return $res->data();
        }
        return $res;
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
