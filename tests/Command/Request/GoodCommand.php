<?php

declare(strict_types=1);

namespace Tests\Command\Request;

use Itantik\Middleware\IRequest;
use Tests\Request\Loggable;

class GoodCommand extends Loggable implements IRequest
{
}
