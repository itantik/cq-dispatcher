<?php

declare(strict_types=1);

namespace Tests\Middleware;

use Itantik\CQDispatcher\Middleware\DataResponse;
use Itantik\Middleware\ILayer;
use Itantik\Middleware\IMiddleware;
use Itantik\Middleware\IRequest;
use Itantik\Middleware\IResponse;
use Tests\Request\Loggable;

class SomeMiddleware implements IMiddleware
{
    /** @var string */
    private $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function handle(IRequest $request, ILayer $nextLayer): IResponse
    {
        if ($request instanceof Loggable) {
            $request->addLog(sprintf('MyMiddleware %s: begin', $this->id));
        }

        /** @var DataResponse $resp */
        $resp = $nextLayer->handle($request);

        $result = $resp->data();
        if ($result instanceof Loggable) {
            $result->addLog(sprintf('MyMiddleware %s: end', $this->id));
        }

        return $resp;
    }
}
