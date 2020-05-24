<?php

declare(strict_types=1);

namespace Itantik\CQDispatcher\Middleware;

use Itantik\CQDispatcher\Command\ICommandHandler;
use Itantik\Middleware\ILayer;
use Itantik\Middleware\IRequest;
use Itantik\Middleware\IResponse;

class CoreCommandLayer implements ILayer
{
    /** @var ICommandHandler */
    private $handler;

    public function __construct(ICommandHandler $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @param IRequest $request
     * @return IResponse
     */
    public function handle(IRequest $request): IResponse
    {
        $this->handler->handle($request);
        return new DataResponse();
    }
}
