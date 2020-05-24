<?php

declare(strict_types=1);

// phpcs:disable PSR1.Files.SideEffects

namespace Tests;

use Itantik\CQDispatcher\Command;
use Itantik\CQDispatcher\Exceptions\HandlerNotFoundException;
use Tester\Assert;
use Tester\TestCase;
use Tests\Command\Request\GoodCommand;
use Tests\Request\LoneRequest;

require_once __DIR__ . '/bootstrap.php';

/**
 * @testCase
 */
class Commands extends TestCase
{
    /** @var CommandsClass */
    private $commands;
    /** @var Command\CommandDispatcher */
    private $dispatcher;

    public function __construct()
    {
        $container = \Tests\Container::create();
        $netteDiContainer = new NetteDIContainer($container);
        $handlerProvider = new Command\DiCommandHandlerProvider($netteDiContainer);
        $this->dispatcher = new Command\CommandDispatcher($handlerProvider);
    }

    public function setUp()
    {
        $this->commands = new CommandsClass($this->dispatcher);
    }

    public function testExecute()
    {
        Assert::exception(function () {
            $command = new LoneRequest();
            $this->commands->execute($command);
        }, HandlerNotFoundException::class);

        $command = new GoodCommand();
        Assert::noError(function () use ($command) {
            $this->commands->execute($command);
        });
        $requestLogs = $command->getLogs();
        Assert::count(1, $requestLogs);
        Assert::same('GoodHandler: handled', $requestLogs[0]);
    }

    public function testWithMiddleware()
    {
        $this->commands->setMiddlewares();

        $command = new GoodCommand();
        Assert::noError(function () use ($command) {
            $this->commands->execute($command);
        });
        $requestLogs = $command->getLogs();
        Assert::count(4, $requestLogs);
        Assert::same('MyMiddleware MW0: begin', $requestLogs[0]);
        Assert::same('MyMiddleware MW1: begin', $requestLogs[1]);
        Assert::same('MyMiddleware MW2: begin', $requestLogs[2]);
        Assert::same('GoodHandler: handled', $requestLogs[3]);
    }
}

(new Commands())->run();
