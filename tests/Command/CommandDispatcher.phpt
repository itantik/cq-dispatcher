<?php

declare(strict_types=1);

// phpcs:disable PSR1.Files.SideEffects

namespace Tests\Command;

use Tests\NetteDIContainer;
use Itantik\CQDispatcher\Command;
use Itantik\CQDispatcher\Exceptions\HandlerNotFoundException;
use Itantik\Middleware\Manager;
use Tester\Assert;
use Tester\TestCase;
use Tests\Command\Request\GoodCommand;
use Tests\Request\LoneRequest;
use Tests\Middleware\SomeMiddleware;

require_once __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class CommandDispatcher extends TestCase
{
    /** @var Command\CommandDispatcher */
    private $dispatcher;
    /** @var Command\DiCommandHandlerProvider */
    private $handlerProvider;

    public function __construct()
    {
        $container = \Tests\Container::create();
        $netteDiContainer = new NetteDIContainer($container);
        $this->handlerProvider = new Command\DiCommandHandlerProvider($netteDiContainer);
    }

    public function setUp()
    {
        $this->dispatcher = new Command\CommandDispatcher($this->handlerProvider);
    }

    public function testDispatch()
    {
        Assert::exception(function () {
            $command = new LoneRequest();
            $this->dispatcher->dispatch($command, null);
        }, HandlerNotFoundException::class);

        $command = new GoodCommand();
        Assert::noError(function () use ($command) {
            $this->dispatcher->dispatch($command, null);
        });
        $requestLogs = $command->getLogs();
        Assert::count(1, $requestLogs);
        Assert::same('GoodHandler: handled', $requestLogs[0]);
    }

    public function testWithMiddleware()
    {
        $manager = new Manager();
        $manager->append(new SomeMiddleware('MW1'));
        $manager->append(new SomeMiddleware('MW2'));
        $manager->prepend(new SomeMiddleware('MW0'));

        $command = new GoodCommand();
        Assert::noError(function () use ($command, $manager) {
            $this->dispatcher->dispatch($command, $manager);
        });
        $requestLogs = $command->getLogs();
        Assert::count(4, $requestLogs);
        Assert::same('MyMiddleware MW0: begin', $requestLogs[0]);
        Assert::same('MyMiddleware MW1: begin', $requestLogs[1]);
        Assert::same('MyMiddleware MW2: begin', $requestLogs[2]);
        Assert::same('GoodHandler: handled', $requestLogs[3]);
    }
}

(new CommandDispatcher())->run();
