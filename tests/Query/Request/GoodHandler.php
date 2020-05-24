<?php

declare(strict_types=1);

namespace Tests\Query\Request;

use Itantik\CQDispatcher\Query\IQueryHandler;
use Tests\Request\Loggable;

class GoodHandler implements IQueryHandler
{
    /**
     * @param GoodQuery $query
     * @return QueryResult
     */
    public function handle(GoodQuery $query)
    {
        if ($query instanceof Loggable) {
            $query->addLog('GoodHandler: handled');
        }

        $res = new QueryResult();
        $res->addLog('GoodHandler: result');
        return $res;
    }
}
