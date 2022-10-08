<?php
namespace Database\Seeders;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        /** @var UserRepositoryInterface $userRepository */
        $userRepository = \App::make(UserRepositoryInterface::class);

        $user = $userRepository->create([
            'name'     => 'TestUser',
            'email'    => 'test@example.com',
            'password' => 'testtest',
        ]);

        $user = $userRepository->create([
            'name'     => 'Test Site Admin',
            'email'    => 'test2@example.com',
            'password' => 'testtest',
        ]);

    }
}
