<?php
namespace EnzanRocket\Foundation\Services\Production;

use EnzanRocket\Foundation\Repositories\AuthenticatableRepositoryInterface;
use EnzanRocket\Foundation\Repositories\PasswordResettableRepositoryInterface;
use EnzanRocket\Foundation\Services\AuthenticatableServiceInterface;
use EnzanRocket\Foundation\Services\MailServiceInterface;
use Illuminate\Support\Arr;

class AuthenticatableService extends BaseService implements AuthenticatableServiceInterface
{
    /** @var \EnzanRocket\Foundation\Repositories\AuthenticatableRepositoryInterface */
    protected $authenticatableRepository;

    /** @var \EnzanRocket\Foundation\Repositories\PasswordResettableRepositoryInterface */
    protected $passwordResettableRepository;

    /** @var string $resetEmailTitle */
    protected $resetEmailTitle = 'Reset Password';

    /** @var string $resetEmailTemplate */
    protected $resetEmailTemplate = '';

    public function __construct(
        AuthenticatableRepositoryInterface $authenticatableRepository,
        PasswordResettableRepositoryInterface $passwordResettableRepository
    ) {
        $this->authenticatableRepository    = $authenticatableRepository;
        $this->passwordResettableRepository = $passwordResettableRepository;
    }

    public function signInById($id)
    {
        /** @var \EnzanRocket\Foundation\Models\AuthenticatableBase $user */
        $user = $this->authenticatableRepository->find($id);
        if (empty($user)) {
            return null;
        }
        $guard = $this->getGuard();
        $guard->login($user);

        return $guard->user();
    }

    /**
     * @return \Illuminate\Contracts\Auth\Guard
     */
    protected function getGuard()
    {
        return \Auth::guard($this->getGuardName());
    }

    /**
     * @return string
     */
    public function getGuardName()
    {
        return null;
    }

    public function signIn($input)
    {
        $rememberMe = (bool) Arr::get($input, 'remember_me', 0);
        $guard      = $this->getGuard();
        if (!$guard->attempt(['email' => Arr::get($input, 'email'), 'password' => Arr::get($input, 'password')], $rememberMe)) {
            return null;
        }

        return $guard->user();
    }

    public function signUp($input)
    {
        $existingUser = $this->authenticatableRepository->findByEmail(Arr::get($input, 'email'));
        if (!empty($existingUser)) {
            return null;
        }

        $user = $this->authenticatableRepository->create($input);
        if (empty($user)) {
            return null;
        }
        $guard = $this->getGuard();
        $guard->login($user);

        return $guard->user();
    }

    public function sendPasswordReset($email)
    {
        return false;
    }

    public function signOut()
    {
        $user = $this->getUser();
        if (empty($user)) {
            return false;
        }
        $guard = $this->getGuard();
        $guard->logout();
        session()->flush();

        return true;
    }

    public function getUser()
    {
        $guard = $this->getGuard();

        return $guard->user();
    }

    public function resignation()
    {
        $user = $this->getUser();
        if (empty($user)) {
            return false;
        }
        $guard = $this->getGuard();
        $guard->logout();
        session()->flush();
        $this->authenticatableRepository->delete($user);

        return true;
    }

    public function sendPasswordResetEmail($email)
    {
        $user = $this->authenticatableRepository->findByEmail($email);
        if (empty($user)) {
            return null;
        }

        $token = $this->passwordResettableRepository->create($user);

        $mailService = app()->make(MailServiceInterface::class);

        $mailService->sendMail(
            $this->resetEmailTitle,
            config('mail.from'),
            ['name' => '', 'address' => $user->email],
            $this->resetEmailTemplate,
            [
                'token' => $token,
                'user'  => $user,
            ]
        );
    }

    public function resetPassword($email, $password, $token)
    {
        $user = $this->authenticatableRepository->findByEmail($email);
        if (empty($user)) {
            return false;
        }
        if (!$this->passwordResettableRepository->exists($user, $token)) {
            return false;
        }
        $this->authenticatableRepository->update($user, ['password' => $password]);
        $this->passwordResettableRepository->delete($user);
        $this->setUser($user);

        return true;
    }

    public function setUser($user)
    {
        $guard = $this->getGuard();
        $guard->login($user);
    }

    public function isSignedIn()
    {
        $guard = $this->getGuard();

        return $guard->check();
    }

    public function createWithImageUrl($input, $imageUrl)
    {
        return $this->authenticatableRepository->create($input);
    }
}
