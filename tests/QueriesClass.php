<?php

declare(strict_types=1);

namespace Tests;

use Tests\Middleware\SomeMiddleware;

class QueriesClass extends \Itantik\CQDispatcher\Queries
{
    public function setMiddlewares()
    {
        $this->clearMiddlewares();
        $this->appendMiddleware(new SomeMiddleware('MW1'));
        $this->appendMiddleware(new SomeMiddleware('MW2'));
        $this->prependMiddleware(new SomeMiddleware('MW0'));
    }
}
