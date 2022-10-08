<?php

declare(strict_types=1);

namespace App\Exceptions\Api\User;

use App\Http\Responses\Api\User\Status;
use Illuminate\Http\JsonResponse;

class APIErrorException extends \App\Exceptions\Api\APIErrorException
{
    public function getErrorResponse(): JsonResponse
    {
        return Status::error($this->errorName, $this->userMessage, $this->extraData)->response();
    }

    protected function errorConfig(): array
    {
        $error = config('api.errors.'.$this->errorName);
        if (empty($error)) {
            $error = config('api.errors.unknown');
        }

        return $error;
    }
}
