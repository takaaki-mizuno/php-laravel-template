<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /** @var array $seeders */
    protected array $seeders = [
    ];

    protected array $environments = [
        'testing'     => [],
        'local'       => [UserSeeder::class],
        'development' => [],
        'production'  => [],
    ];


    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->seeders as $seeder) {
            $this->call($seeder);
        }
        if (array_key_exists(app()->environment(), $this->environments)) {
            foreach ($this->environments[app()->environment()] as $seeder) {
                $this->call($seeder);
            }
        }
    }
}
