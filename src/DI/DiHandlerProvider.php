<?php

declare(strict_types=1);

namespace Itantik\CQDispatcher\DI;

use Exception;
use Itantik\CQDispatcher\Exceptions\HandlerNotFoundException;
use Itantik\Middleware\IRequest;

class DiHandlerProvider
{
    /** @var IContainer */
    private $container;
    /** @var string - mandatory handler suffix */
    private $handlerSuffix = 'Handler';
    /** @var string - optional command suffix */
    private $requestSuffix = 'Request';


    public function __construct(IContainer $container)
    {
        $this->container = $container;
    }

    /**
     * @param IRequest $request
     * @return object
     * @throws HandlerNotFoundException
     */
    public function createHandler(IRequest $request): object
    {
        // remove request suffix
        $rc = get_class($request);
        $reqClass = $this->requestSuffix ? preg_replace('/^(.*[^\\\\])' . $this->requestSuffix . '$/', '$1', $rc) : $rc;
        // add handler suffix
        $hc = $reqClass . $this->handlerSuffix;
        if (!class_exists($hc)) {
            throw new HandlerNotFoundException(sprintf("Handler class '%s' not found.", $hc));
        }

        try {
            $handler = $this->container->get($hc);
        } catch (Exception $e) {
            throw new HandlerNotFoundException(
                sprintf("Cannot create an instance of '%s'.", $hc)
            );
        }

        if (!method_exists($handler, 'handle')) {
            throw new HandlerNotFoundException(sprintf("'%s'::handle(\$request) method missing.", get_class($handler)));
        }

        return $handler;
    }

    /**
     * Mandatory handler suffix.
     * @param string $handlerSuffix
     */
    public function setHandlerSuffix(string $handlerSuffix): void
    {
        if (!$handlerSuffix) {
            throw new \InvalidArgumentException('Handler suffix must not be empty.');
        }
        if ($handlerSuffix === $this->requestSuffix) {
            throw new \InvalidArgumentException('Request and handler suffix must be different.');
        }
        $this->handlerSuffix = $handlerSuffix;
    }

    /**
     * Optional command suffix. Valid class names: SomeRequest or Some.
     * @param string $requestSuffix
     */
    public function setRequestSuffix(string $requestSuffix): void
    {
        if ($requestSuffix === $this->handlerSuffix) {
            throw new \InvalidArgumentException('Request and handler suffix must be different.');
        }
        $this->requestSuffix = $requestSuffix;
    }
}
