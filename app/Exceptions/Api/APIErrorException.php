<?php

declare(strict_types=1);

namespace App\Exceptions\Api;

use Exception;

class APIErrorException extends Exception
{
    protected string $userMessage = '';

    protected string $errorName = '';

    protected array $extraData = [];

    protected array $config = [];

    /**
     * APIErrorException constructor.
     */
    public function __construct(string $error, string $message, array $extraData = [])
    {
        $this->errorName = $error;
        $this->userMessage = $message;
        $this->extraData = $extraData;
        $this->config = $this->errorConfig();
        parent::__construct($message, $this->config['code'], null);
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
