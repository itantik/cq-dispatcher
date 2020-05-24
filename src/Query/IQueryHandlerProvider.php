<?php

declare(strict_types=1);

namespace Itantik\CQDispatcher\Query;

use Itantik\Middleware\IRequest;

interface IQueryHandlerProvider
{
    public function createHandler(IRequest $query): IQueryHandler;
}
