<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\Status as StatusRequest;
use App\Http\Responses\Api\User\Status as StatusResponse;
use Illuminate\Http\JsonResponse;

class IndexController extends Controller
{
    public function status(StatusRequest $request): JsonResponse
    {
        $stats = $request->get('status');

        return StatusResponse::ok()->response();
    }
}
