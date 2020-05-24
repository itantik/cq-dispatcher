<?php

declare(strict_types=1);

namespace Itantik\CQDispatcher\Middleware;

use Itantik\CQDispatcher\Exceptions\MiddlewareException;
use Itantik\Middleware\ILayer;
use Itantik\Middleware\IMiddleware;
use Itantik\Middleware\IRequest;
use Itantik\Middleware\IResponse;

final class DataTransportLayer implements ILayer
{
    /** @var IMiddleware */
    private $middleware;
    /** @var ILayer */
    private $nextLayer;

    public function __construct(IMiddleware $middleware, ILayer $nextLayer)
    {
        $this->middleware = $middleware;
        $this->nextLayer = $nextLayer;
    }

    /**
     * @param IRequest $request
     * @return IResponse
     * @throws MiddlewareException
     */
    public function handle(IRequest $request): IResponse
    {
        $res = $this->middleware->handle($request, $this->nextLayer);
        if (!($res instanceof DataResponse)) {
            throw new MiddlewareException(
                sprintf("Middleware handler must return an instance of '%s'.", DataResponse::class)
            );
        }
        return $res;
    }
}
