<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class Resource extends JsonResource
{
    public static $wrap;

    protected array $columns = [];
    protected array $optionalColumns = [];
    protected int $statusCode = 200;

    public function withStatus(int $statusCode): static
    {
        $this->statusCode = (int) $statusCode;

        return $this;
    }

    public function withResponse($request, $response): void
    {
        $response->setStatusCode($this->statusCode);
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $ret = [];
        $resourceArray = \is_array($this->resource) ? $this->resource : $this->resource->toArray();
        foreach (array_keys($this->columns) as $column) {
            if (\array_key_exists($column, $resourceArray)) {
                if ($resourceArray[$column] instanceof self) {
                    $ret[$column] = $resourceArray[$column]->toArray($request);
                } elseif (\is_array($resourceArray[$column])) {
                    $ret[$column] = [];
                    foreach ($resourceArray[$column] as $item) {
                        if ($item instanceof self) {
                            $ret[$column][] = $item->toArray($request);
                        } else {
                            $ret[$column][] = $item;
                        }
                    }
                } else {
                    $ret[$column] = $resourceArray[$column];
                }
            } elseif (!\in_array($column, $this->optionalColumns, true)) {
                $ret[$column] = $this->columns[$column];
            }
        }

        return $ret;
    }
}
