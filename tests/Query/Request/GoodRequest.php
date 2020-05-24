<?php

declare(strict_types=1);

namespace Tests\Query\Request;

use Itantik\Middleware\IRequest;
use Tests\Request\Loggable;

class GoodRequest extends Loggable implements IRequest
{
}
