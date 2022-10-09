<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\User;

use App\Http\Resources\Resource;

class Me extends Resource
{
    protected array $columns = [
        'id' => 0,
        'name' => '',
        'email' => '',
    ];
}
