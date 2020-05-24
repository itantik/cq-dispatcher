<?php

declare(strict_types=1);

namespace Tests\Query\Request;

use Itantik\CQDispatcher\Query\IQueryHandler;

class GoodRequestHandler implements IQueryHandler
{
    public function handle(GoodRequest $query)
    {
    }
}
