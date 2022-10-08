<?php

declare(strict_types=1);

namespace App\Http\Responses\Api\User;

use App\Http\Responses\Response as ResponseBase;
use App\Models\Base;

class Response extends ResponseBase
{
    protected array $columns = [];

    public static function updateWithModel(Base $model): static
    {
        return new static([]);
    }

    protected static function date($date): ?string
    {
        if ($date instanceof \DateTime) {
            return $date->format('Y-m-d');
        }

        return null;
    }

    protected static function dateTime($dateTime): ?string
    {
        if ($dateTime instanceof \DateTime) {
            return $dateTime->format('U');
        }

        return null;
    }

    protected static function generateArray(array $items, string $class): array
    {
        $ret = [];
        foreach ($items as $item) {
            $ret[] = $class::updateWithModel($item);
        }

        return $ret;
    }
}
