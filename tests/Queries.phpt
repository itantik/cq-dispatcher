<?php

declare(strict_types=1);

// phpcs:disable PSR1.Files.SideEffects

namespace Tests;

use Itantik\CQDispatcher\Query;
use Itantik\CQDispatcher\Exceptions\HandlerNotFoundException;
use Tester\Assert;
use Tester\TestCase;
use Tests\Query\Request\GoodQuery;
use Tests\Request\LoneRequest;
use Tests\Query\Request\QueryResult;

require_once __DIR__ . '/bootstrap.php';

/**
 * @testCase
 */
class Queries extends TestCase
{
    /** @var QueriesClass */
    private $queries;
    /** @var Query\QueryDispatcher */
    private $dispatcher;

    public function __construct()
    {
        $container = \Tests\Container::create();
        $netteDiContainer = new NetteDIContainer($container);
        $handlerProvider = new Query\DiQueryHandlerProvider($netteDiContainer);
        $this->dispatcher = new Query\QueryDispatcher($handlerProvider);
    }

    public function setUp()
    {
        $this->queries = new QueriesClass($this->dispatcher);
    }

    public function testExecute()
    {
        Assert::exception(function () {
            $query = new LoneRequest();
            $this->queries->execute($query);
        }, HandlerNotFoundException::class);

        $query = new GoodQuery();
        Assert::noError(function () use ($query) {
            $this->queries->execute($query);
        });

        $query = new GoodQuery();
        $result = $this->queries->execute($query);

        Assert::type(QueryResult::class, $result);

        $requestLogs = $query->getLogs();
        Assert::count(1, $requestLogs);
        Assert::same('GoodHandler: handled', $requestLogs[0]);

        /** @var QueryResult $result */
        $resultLogs = $result->getLogs();
        Assert::count(1, $resultLogs);
        Assert::same('GoodHandler: result', $resultLogs[0]);
    }

    public function testWithMiddleware()
    {
        $this->queries->setMiddlewares();

        $query = new GoodQuery();
        Assert::noError(function () use ($query) {
            $this->queries->execute($query);
        });

        $query = new GoodQuery();
        $result = $this->queries->execute($query);

        Assert::type(QueryResult::class, $result);

        $requestLogs = $query->getLogs();
        Assert::count(4, $requestLogs);
        Assert::same('MyMiddleware MW0: begin', $requestLogs[0]);
        Assert::same('MyMiddleware MW1: begin', $requestLogs[1]);
        Assert::same('MyMiddleware MW2: begin', $requestLogs[2]);
        Assert::same('GoodHandler: handled', $requestLogs[3]);

        /** @var QueryResult $result */
        $resultLogs = $result->getLogs();
        Assert::count(4, $resultLogs);
        Assert::same('GoodHandler: result', $resultLogs[0]);
        Assert::same('MyMiddleware MW2: end', $resultLogs[1]);
        Assert::same('MyMiddleware MW1: end', $resultLogs[2]);
        Assert::same('MyMiddleware MW0: end', $resultLogs[3]);
    }
}

(new Queries())->run();
