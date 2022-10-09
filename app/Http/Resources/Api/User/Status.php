<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\User;

use App\Http\Resources\Resource;
use Illuminate\Support\Arr;

class Status extends Resource
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

    public static function ok($message = '', $extraData = [], $statusCode = 200): static
    {
        $static = new static([
            'success' => true,
            'type' => Arr::get($extraData, 'type', ''),
            'title' => Arr::get($extraData, 'title', ''),
            'status' => (int) Arr::get($extraData, 'status', 0),
            'errorCode' => 0,
            'detail' => $message,
            'invalidParams' => [],
        ]);
        $static->withStatus($statusCode);

        return $static;
    }

    public static function error($error, $message, $extraData = []): static
    {
        $error = config('api.errors.'.$error);
        if (empty($error)) {
            $error = config('api.errors.unknown');
        }

        $static = new static([
            'success' => false,
            'type' => Arr::get($extraData, 'type', ''),
            'title' => Arr::get($extraData, 'title', ''),
            'status' => (int) Arr::get($extraData, 'status', 0),
            'errorCode' => (int) Arr::get($error, 'code'),
            'detail' => empty($message) ? Arr::get($error, 'message', '') : $message,
            'invalidParams' => Arr::get($extraData, 'invalidParams', []),
        ]);
        $static->withStatus(Arr::get($error, 'statusCode', 400));

        return $static;
    }
}
