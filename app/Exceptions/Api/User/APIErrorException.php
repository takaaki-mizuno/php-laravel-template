<?php

declare(strict_types=1);

namespace App\Exceptions\Api\User;

use App\Http\Resources\Api\User\Status;

class APIErrorException extends \App\Exceptions\Api\APIErrorException
{
    public function getErrorResponse(): Status
    {
        return Status::error($this->errorName, $this->userMessage, $this->extraData);
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
