<?php

declare(strict_types=1);

namespace Itantik\CQDispatcher\Middleware;

use Itantik\Middleware\ILayer;
use Itantik\Middleware\IMiddleware;
use Itantik\Middleware\ITransportLayerFactory;

final class DataTransportLayerFactory implements ITransportLayerFactory
{
    public function create(IMiddleware $middleware, ILayer $nextLayer): ILayer
    {
        return new DataTransportLayer($middleware, $nextLayer);
    }
}
