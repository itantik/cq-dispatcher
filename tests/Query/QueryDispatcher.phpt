<?php

declare(strict_types=1);

// phpcs:disable PSR1.Files.SideEffects

namespace Tests\Query;

use Tests\NetteDIContainer;
use Itantik\CQDispatcher\Middleware\DataResponse;
use Itantik\CQDispatcher\Query;
use Itantik\CQDispatcher\Exceptions\HandlerNotFoundException;
use Itantik\Middleware\Manager;
use Tester\Assert;
use Tester\TestCase;
use Tests\Query\Request\GoodQuery;
use Tests\Request\LoneRequest;
use Tests\Middleware\SomeMiddleware;
use Tests\Query\Request\QueryResult;

require_once __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class QueryDispatcher extends TestCase
{
    /** @var Query\QueryDispatcher */
    private $dispatcher;
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
        $this->dispatcher = new Query\QueryDispatcher($this->handlerProvider);
    }

    public function testDispatch()
    {
        Assert::exception(function () {
            $query = new LoneRequest();
            $this->dispatcher->dispatch($query, null);
        }, HandlerNotFoundException::class);

        $query = new GoodQuery();
        Assert::noError(function () use ($query) {
            $this->dispatcher->dispatch($query, null);
        });

        $query = new GoodQuery();
        $result = $this->dispatcher->dispatch($query, null);

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
        $manager = new Manager();
        $manager->append(new SomeMiddleware('MW1'));
        $manager->append(new SomeMiddleware('MW2'));
        $manager->prepend(new SomeMiddleware('MW0'));

        $query = new GoodQuery();
        Assert::noError(function () use ($query, $manager) {
            $this->dispatcher->dispatch($query, $manager);
        });

        $query = new GoodQuery();
        $result = $this->dispatcher->dispatch($query, $manager);

        Assert::type(DataResponse::class, $result);

        $requestLogs = $query->getLogs();
        Assert::count(4, $requestLogs);
        Assert::same('MyMiddleware MW0: begin', $requestLogs[0]);
        Assert::same('MyMiddleware MW1: begin', $requestLogs[1]);
        Assert::same('MyMiddleware MW2: begin', $requestLogs[2]);
        Assert::same('GoodHandler: handled', $requestLogs[3]);

        /** @var DataResponse $result */
        $resultData = $result->data();
        Assert::type(QueryResult::class, $resultData);

        /** @var QueryResult $resultData */
        $resultLogs = $resultData->getLogs();
        Assert::count(4, $resultLogs);
        Assert::same('GoodHandler: result', $resultLogs[0]);
        Assert::same('MyMiddleware MW2: end', $resultLogs[1]);
        Assert::same('MyMiddleware MW1: end', $resultLogs[2]);
        Assert::same('MyMiddleware MW0: end', $resultLogs[3]);
    }
}

(new QueryDispatcher())->run();
