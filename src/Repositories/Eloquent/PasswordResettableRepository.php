<?php

declare(strict_types=1);

namespace EnzanRocket\Foundation\Repositories\Eloquent;

use Illuminate\Auth\Passwords\DatabaseTokenRepository;
use Illuminate\Database\ConnectionInterface;
use EnzanRocket\Foundation\Repositories\PasswordResettableRepositoryInterface;

class PasswordResettableRepository extends DatabaseTokenRepository implements PasswordResettableRepositoryInterface
{
    protected string $tableName = 'password_resets';

    protected int $expiresIn = 60;

    public function __construct()
    {
        parent::__construct(
            $this->getDatabaseConnection(),
            app()['hash'],
            $this->tableName,
            (string) config('app.key'), // Use app key for token hashing — previously 'random' which was insecure
            $this->expiresIn
        );
    }

    protected function getDatabaseConnection(): ConnectionInterface
    {
        return app()['db']->connection();
    }
}
