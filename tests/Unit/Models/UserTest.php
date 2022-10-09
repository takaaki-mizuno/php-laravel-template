<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Contracts\Container\BindingResolutionException;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * Test User create.
     *
     * @return void
     */
    public function test_create_user_model_successfully(): void
    {
        $user = new User();
        $this->assertNotNull($user);
    }

    public function test_create_new_user_and_store_it_successfully(): void
    {
        $userModel = new User();

        $userData = User::factory()->make();
        foreach ($userData->toFillableArray() as $key => $value) {
            $userModel->$key = $value;
        }
        $userModel->save();
        $this->assertNotNull(User::find($userModel->id));
    }
}
