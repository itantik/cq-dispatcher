<?php

declare(strict_types=1);

namespace Tests\Middleware;

use Itantik\Middleware\ILayer;
use Itantik\Middleware\IMiddleware;
use Itantik\Middleware\IRequest;
use Itantik\Middleware\IResponse;

class BadMiddleware implements IMiddleware
{
    public function handle(IRequest $request, ILayer $nextLayer): IResponse
    {
        // must return DataResponse
        return new Response();
    }
}
