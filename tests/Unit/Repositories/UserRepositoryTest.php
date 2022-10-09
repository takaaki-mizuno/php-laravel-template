<?php

namespace Tests\Unit\Repositories;


use App\Contracts\Repositories\UserRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    /**
     * Test UserRepository create.
     *
     * @return void
     * @throws BindingResolutionException
     */
    public function test_create_user_repository_instance_successfully(): void
    {
        $repository = app()->make(UserRepositoryInterface::class);
        $this->assertNotNull($repository);
    }
}
