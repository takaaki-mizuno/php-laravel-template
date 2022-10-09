<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\Status as StatusRequest;
use App\Http\Resources\Api\User\Status;

class IndexController extends Controller
{
    public function status(StatusRequest $request): Status
    {
        $stats = $request->get('status');

        return Status::ok();
    }
}
