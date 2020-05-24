<?php

declare(strict_types=1);

namespace Itantik\CQDispatcher\Query;

use Itantik\Middleware\IRequest;
use Itantik\Middleware\Manager;

interface IQueryDispatcher
{
    /**
     * @param IRequest $query
     * @param Manager|null $middlewareManager
     * @return mixed
     */
    public function dispatch(IRequest $query, ?Manager $middlewareManager);
}
