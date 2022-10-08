<?php

declare(strict_types=1);

namespace App\Http\Responses\Api\User;

use App\Models\Base;
use App\Models\User;

class Me extends Response
{
    protected array $columns = [
        'id' => 0,
        'name' => '',
        'email' => '',
        'profileImage' => [],
    ];

    public static function updateWithModel(Base|User $model): static
    {
        $response = new static([], 400);
        if (!empty($model)) {
            $modelArray = [
                'id' => $model->id,
                'name' => $model->name,
                'email' => $model->email,
            ];
            $response = new static($modelArray, 200);
        }

        return $response;
    }
}
