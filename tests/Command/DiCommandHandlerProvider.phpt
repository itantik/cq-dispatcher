<?php

declare(strict_types=1);

// phpcs:disable PSR1.Files.SideEffects

namespace Tests\Command;

use Tests\NetteDIContainer;
use Itantik\CQDispatcher\Command;
use Itantik\CQDispatcher\Exceptions\HandlerNotFoundException;
use Tester\Assert;
use Tester\TestCase;
use Tests\Command\Request\BadHandlerRequest;
use Tests\Command\Request\BadHandlerRequest2;
use Tests\Command\Request\GoodCommand;
use Tests\Command\Request\GoodHandler;
use Tests\Command\Request\GoodRequest;
use Tests\Command\Request\GoodRequestHandler;
use Tests\Request\LoneRequest;

require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../Container.php';

/**
 * @testCase
 */
class DiCommandHandlerProvider extends TestCase
{
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
    }

    public function testMissingHandler()
    {
        Assert::exception(function () {
            $command = new LoneRequest();
            $this->handlerProvider->createHandler($command);
        }, HandlerNotFoundException::class, "Handler class 'Tests\Request\LoneRequestHandler' not found.");

        Assert::exception(function () {
            $command = new BadHandlerRequest();
            $this->handlerProvider->createHandler($command);
        }, HandlerNotFoundException::class, "'Tests\Command\Request\BadHandlerRequestHandler'::handle(\$request) method missing.");

        Assert::exception(function () {
            $command = new BadHandlerRequest2();
            $this->handlerProvider->createHandler($command);
        }, HandlerNotFoundException::class, "Command handler 'Tests\Command\Request\BadHandlerRequest2Handler' must implement 'Itantik\CQDispatcher\Command\ICommandHandler'.");
    }

    public function testCreateHandler()
    {
        $command = new GoodRequest();
        Assert::type(GoodRequestHandler::class, $this->handlerProvider->createHandler($command));

        $command = new GoodCommand();
        Assert::type(GoodHandler::class, $this->handlerProvider->createHandler($command));
    }
}

(new DiCommandHandlerProvider())->run();
