<?php

declare(strict_types=1);

namespace App\Http\Responses\Api\User;

use Illuminate\Support\Arr;

class Status extends Response
{
    protected array $columns = [
        'success' => true,
        'type' => '',
        'title' => '',
        'status' => '',
        'errorCode' => 0,
        'detail' => '',
        'invalidParams' => [],
    ];

    protected array $optionalColumns = [
        'type',
        'title',
        'status',
        'errorCode',
        'detail',
        'invalidParams',
    ];

    public static function error($error, $message, $extraData = []): static
    {
        $error = config('api.errors.'.$error);
        if (empty($error)) {
            $error = config('api.errors.unknown');
        }

        return new static([
            'isSuccess' => false,
            'type' => Arr::get($extraData, 'type', ''),
            'title' => Arr::get($extraData, 'title', ''),
            'status' => (int) Arr::get($extraData, 'status', 0),
            'errorCode' => (int) Arr::get($error, 'code'),
            'detail' => empty($message) ? Arr::get($error, 'message', '') : $message,
            'invalidParams' => Arr::get($extraData, 'invalidParams', []),
        ], Arr::get($error, 'statusCode', 400));
    }

    public static function ok($message = '', $extraData = [], $statusCode = 200): static
    {
        return new static([
            'isSuccess' => true,
            'type' => Arr::get($extraData, 'type', ''),
            'title' => Arr::get($extraData, 'title', ''),
            'status' => (int) Arr::get($extraData, 'status', 0),
            'errorCode' => 0,
            'detail' => $message,
            'invalidParams' => [],
        ], $statusCode);
    }
}
