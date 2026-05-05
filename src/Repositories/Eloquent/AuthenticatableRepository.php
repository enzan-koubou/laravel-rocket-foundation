<?php

declare(strict_types=1);

namespace EnzanRocket\Foundation\Repositories\Eloquent;

use EnzanRocket\Foundation\Models\AuthenticatableBase;
use EnzanRocket\Foundation\Repositories\AuthenticatableRepositoryInterface;
use Illuminate\Support\Facades\DB;

class AuthenticatableRepository extends SingleKeyModelRepository implements AuthenticatableRepositoryInterface
{
    public function getBlankModel(): AuthenticatableBase
    {
        return new AuthenticatableBase();
    }

    public function findByEmail(string $email): ?AuthenticatableBase
    {
        $className = $this->getModelClassName();

        return $className::whereEmail($email)->first();
    }

    /**
     * ⚠️ DEPRECATED: findByFacebookId() is not declared in AuthenticatableRepositoryInterface.
     * Verify whether this is still used before removing.
     */
    public function findByFacebookId(string $facebookId): ?AuthenticatableBase
    {
        $className = $this->getModelClassName();

        return $className::whereFacebookId($facebookId)->first();
    }

    public function updateRawPassword(AuthenticatableBase $user, string $password): ?AuthenticatableBase
    {
        $table = $this->getBlankModel()->getTable();
        DB::table($table)->where('id', $user->id)->update(['password' => $password]);

        return $this->find($user->id);
    }
}
