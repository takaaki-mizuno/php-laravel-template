<?php

declare(strict_types=1);

namespace App\Contracts\Services;

use LaravelRocket\Foundation\Services\AuthenticatableServiceInterface;

interface UserServiceInterface extends AuthenticatableServiceInterface
{
    /**
     * @return \App\Models\User
     */
    public function getUser();
}
