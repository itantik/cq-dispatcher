<?php

declare(strict_types=1);

// phpcs:disable PSR1.Files.SideEffects

namespace Tests\Query;

use Tests\NetteDIContainer;
use Itantik\CQDispatcher\Query;
use Itantik\CQDispatcher\Exceptions\HandlerNotFoundException;
use Tester\Assert;
use Tester\TestCase;
use Tests\Query\Request\BadHandlerRequest;
use Tests\Query\Request\BadHandlerRequest2;
use Tests\Query\Request\GoodQuery;
use Tests\Query\Request\GoodHandler;
use Tests\Query\Request\GoodRequest;
use Tests\Query\Request\GoodRequestHandler;
use Tests\Request\LoneRequest;

require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../Container.php';

/**
 * @testCase
 */
class DiQueryHandlerProvider extends TestCase
{
    /** @var Query\DiQueryHandlerProvider */
    private $handlerProvider;

    public function __construct()
    {
        $container = \Tests\Container::create();
        $netteDiContainer = new NetteDIContainer($container);
        $this->handlerProvider = new Query\DiQueryHandlerProvider($netteDiContainer);
    }

    public function setUp()
    {
    }

    public function testMissingHandler()
    {
        Assert::exception(function () {
            $query = new LoneRequest();
            $this->handlerProvider->createHandler($query);
        }, HandlerNotFoundException::class, "Handler class 'Tests\Request\LoneRequestHandler' not found.");

        Assert::exception(function () {
            $query = new BadHandlerRequest();
            $this->handlerProvider->createHandler($query);
        }, HandlerNotFoundException::class, "'Tests\Query\Request\BadHandlerRequestHandler'::handle(\$request) method missing.");

        Assert::exception(function () {
            $query = new BadHandlerRequest2();
            $this->handlerProvider->createHandler($query);
        }, HandlerNotFoundException::class, "Query handler 'Tests\Query\Request\BadHandlerRequest2Handler' must implement 'Itantik\CQDispatcher\Query\IQueryHandler'.");
    }

    public function testCreateHandler()
    {
        $query = new GoodRequest();
        Assert::type(GoodRequestHandler::class, $this->handlerProvider->createHandler($query));

        $query = new GoodQuery();
        Assert::type(GoodHandler::class, $this->handlerProvider->createHandler($query));
    }
}

(new DiQueryHandlerProvider())->run();
