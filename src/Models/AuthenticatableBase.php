<?php
namespace EnzanRocket\Foundation\Models;

/*
 * App\Models\AuthenticatableBase
 *
 * @property string $password
 * @property int $profile_image_id
 * @property string $api_access_token
 */

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use EnzanRocket\Foundation\Models\Traits\LocaleStorable;

class AuthenticatableBase extends Base implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword, LocaleStorable;

    public function setPasswordAttribute($password): void
    {
        if (empty($password)) {
            $this->attributes['password'] = '';
        } elseif (password_get_info((string) $password)['algoName'] === 'unknown') {
            // Plain-text password — hash it before storing
            $this->attributes['password'] = app('hash')->make($password);
        } else {
            // Already hashed (e.g. from a factory, seeder, or Laravel's rehashPasswordIfRequired)
            $this->attributes['password'] = $password;
        }
    }
}
