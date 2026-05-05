<?php

declare(strict_types=1);

namespace EnzanRocket\Foundation\Repositories;

use EnzanRocket\Foundation\Models\AuthenticatableBase;

interface AuthenticatableRepositoryInterface extends SingleKeyModelRepositoryInterface
{
    public function findByEmail(string $email): ?AuthenticatableBase;

    public function updateRawPassword(AuthenticatableBase $user, string $password): ?AuthenticatableBase;
}
