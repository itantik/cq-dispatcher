# CQ Dispatcher

Command Query Dispatcher with middleware support. This library can have its place in applications that are designed according to the principles of _Command Query Separation_ (CQS) or _Command Query Responsibility Segregation_ (CQRS).

CQ Dispatcher is typically located in the _application service layer_, its clients are controllers and presenters from _UI layer_. It dispatches application requests, these are commands and queries, finds an appropriate handler and executes it.

**Command** represents a request, that performs an action through a command handler. It **modifies** data or changes the state of objects. It **doesn't return** a value.

**Query** represents a request, that gets a data through a query handler. It **must not modify** data. It **returns** a value.

## Installation

```
composer require itantik/cq-dispatcher
```

## Usage

CQ Dispatcher requires a dependency injection container. You have to define an adapter to your DI container. Adapter implements `Itantik\CQDispatcher\DI\IContainer`.

#### Usage with Nette framework

Install [itantik/nette-cq-dispatcher](https://github.com/itantik/nette-cq-dispatcher) extension for [Nette framework](https://nette.org/). It is configured to use Nette DI Container.

### Example of file structure

```
- UserService
    - UserCommands.php  // command dispatcher
    - UserQueries.php   // query dispatcher
    - Command
        - AddUserCommand.php
        - AddUserHandler.php
        - ChangePasswordCommand.php
        - ChangePasswordHandler.php
        - ... // other commands/handlers
    - Query
        - FindAllUsersQuery.php
        - FindAllUsersHandler.php
        - GetUserQuery.php
        - GetUserHandler.php
        - ... // other queries/handlers
- Middleware
    - TransactionalMiddleware.php
```

### Command

Command is a plain object that implements `Itantik\Middleware\IRequest` interface and class name uses optional `Command` suffix. The command object represents your request.

```php
class AddUserCommand implements IRequest
{
    /** @var int */
    private $id;
    /** @var string */
    private $name;
    /** @var string */
    private $surname;

    public function __construct(int $id, string $name, string $surname)
    {
        $this->id = $id;
        $this->name = $name;
        $this->surname = $surname;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function surname(): string
    {
        return $this->surname;
    }
}
```

### Command Handler

Command handler implements `Itantik\CQDispatcher\Command\ICommandHandler` interface and class name should use `Handler` suffix. Command handler represents an application service.

```php
class AddUserHandler implements ICommandHandler
{
    /** @var UserRepository */
    private $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(AddUserCommand $command): void
    {
        $user = new User($command->id(), $command->name(), $command->surname());
        $this->repository->add($user);
    }
}
```

### Command Dispatcher

Command dispatcher creates an appropriate command handler based on the command class name, then invokes its `handle` method. For each command, there is a single command handler.

By default, pairing command with its handler is based on class names.

Command `Namespace\SomeCommand` uses `Namespace\SomeHandler` handler. `Command` suffix is optional, so you can name command as `Namespace\Some` instead of `Namespace\SomeCommand`.

#### Creating Command Dispatcher

Command dispatcher extends abstract class `\Itantik\CQDispatcher\Commands`.

```php
class UserCommands extends \Itantik\CQDispatcher\Commands
{
}
```

#### Using in Controllers and Presenters

```php
// simplified code

class UserController
{
    /** @var UserCommands */
    private $userCommands;

    public function actionAddUser(string $name, string $surname): void
    {
        // create a command
        $id = SomeIdGenerator::next();
        $command = new AddUserCommand($id, $name, $surname);
        try {
            // execute command
           $this->userCommands->execute($command);
        } catch (\Exception $ex) {
            // handle error
            // ...
        }
        // redirect to user detail page
        // ...
    }
}
```

#### Extending with Middleware

Command dispatcher has built-in [itantik/middleware](https://github.com/itantik/middleware) support.

For example, middleware for wrapping each command handler with a database transaction can look like this:

```php
final class TransactionalMiddleware implements Itantik\Middleware\IMiddleware
{
    /** @var DatabaseConnection */
    private $connection;


    public function __construct(DatabaseConnection $connection)
    {
        $this->connection = $connection;
    }

    public function handle(IRequest $request, ILayer $nextLayer): IResponse
    {
        $connection = $this->connection;
        $connection->beginTransaction();
        try {
            $res = $nextLayer->handle($request);
            $connection->commit();
            return $res;
        } catch (Exception $e) {
            $connection->rollback();
            throw $e;
        }
    }
}
```

Extending command dispatcher:

```php
class UserCommands extends \Itantik\CQDispatcher\Commands
{
    public function __construct(
        ICommandDispatcher $commandDispatcher,
        DatabaseConnection $connection
    ) {
        parent::__construct($commandDispatcher);
        $this->appendMiddleware(new TransactionalMiddleware($connection));
    }
}
```

Using in controller is not changed. Now each command is executed in database transaction.

### Query

Query implements `Itantik\Middleware\IRequest` interface and class name uses `Query` suffix (optional).

```php
class FindAllUsersQuery implements IRequest
{
}
```

### Query Handler

Query handler implements `Itantik\CQDispatcher\Query\IQueryHandler` interface, class name should use `Handler` suffix, `handle` method returns a value.

```php
class FindAllUsersHandler implements IQueryHandler
{
    /** @var UserRepository */
    private $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(FindAllUsersQuery $query): UserList
    {
        return $this->repository->findAll();
    }
}
```

### Query Dispatcher

Similar to the command dispatcher.

Query `Namespace\SomeQuery` uses `Namespace\SomeHandler` handler. `Query` suffix is optional, so you can name query as `Namespace\Some` instead of `Namespace\SomeQuery`.

#### Creating Query Dispatcher

Query dispatcher extends abstract class `\Itantik\CQDispatcher\Queries`.

```php
class UserQueries extends \Itantik\CQDispatcher\Queries
{
}
```

#### Using in Controllers and Presenters

```php
// simplified code

class UserController
{
    /** @var UserQueries */
    private $userQueries;

    public function actionAllUsers(): void
    {
        // create a query
        $query = new FindAllUsersQuery();
        // execute query
       $userList = $this->userQueries->execute($query);

        // fill-in template
        // ...
    }
}
```

#### Extending with Middleware

The same as with the Command dispatcher.

# Requirements

- PHP 7.2
