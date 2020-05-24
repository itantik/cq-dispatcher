<?php

declare(strict_types=1);

namespace Itantik\CQDispatcher\Middleware;

use Itantik\CQDispatcher\Query\IQueryHandler;
use Itantik\Middleware\ILayer;
use Itantik\Middleware\IRequest;
use Itantik\Middleware\IResponse;

class CoreQueryLayer implements ILayer
{
    /** @var IQueryHandler */
    private $handler;

    public function __construct(IQueryHandler $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @param IRequest $request
     * @return IResponse
     */
    public function handle(IRequest $request): IResponse
    {
        $res = $this->handler->handle($request);
        return new DataResponse($res);
    }
}
