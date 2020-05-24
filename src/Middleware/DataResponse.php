<?php

declare(strict_types=1);

namespace Itantik\CQDispatcher\Middleware;

use Itantik\Middleware\IResponse;

class DataResponse implements IResponse
{
    /** @var mixed $data */
    private $data;

    /**
     * @param mixed|null $data
     */
    public function __construct($data = null)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function data()
    {
        return $this->data;
    }
}
