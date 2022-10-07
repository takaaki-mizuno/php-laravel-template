<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class Response
{
    protected array $columns = [];

    protected array $optionalColumns = [];

    /**
     * @var int
     */
    protected mixed $statusCode = 200;

    protected array $data = [];

    public function __construct($initialValues, $statusCode = 200)
    {
        foreach (array_keys($this->columns) as $column) {
            if (\array_key_exists($column, $initialValues)) {
                $this->data[$column] = $initialValues[$column];
            } elseif (!\in_array($column, $this->optionalColumns, true)) {
                $this->data[$column] = $this->columns[$column];
            }
        }
        $this->statusCode = $statusCode;
    }

    public function set(string $name, mixed $value): void
    {
        if (\array_key_exists($name, $this->columns)) {
            $this->data[$name] = $value;
        }
    }

    /**
     * @return null|mixed
     */
    public function get(string $name, mixed $default = null): mixed
    {
        if (!\array_key_exists($name, $this->columns)) {
            return $default;
        }
        if (\array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        if (null === $default) {
            return $this->columns[$name];
        }

        return $default;
    }

    /**
     * @return $this
     */
    public function withStatus(int $statusCode): static
    {
        $this->statusCode = (int) $statusCode;

        return $this;
    }

    public function response(): JsonResponse
    {
        return response()->json($this->toArray(), $this->statusCode);
    }

    public function toArray(): array
    {
        $ret = [];
        foreach (array_keys($this->columns) as $column) {
            if (\array_key_exists($column, $this->data)) {
                if ($this->data[$column] instanceof self) {
                    $ret[$column] = $this->data[$column]->toArray();
                } elseif (\is_array($this->data[$column])) {
                    $ret[$column] = [];
                    foreach ($this->data[$column] as $item) {
                        if ($item instanceof self) {
                            $ret[$column][] = $item->toArray();
                        } else {
                            $ret[$column][] = $item;
                        }
                    }
                } else {
                    $ret[$column] = $this->data[$column];
                }
            } elseif (!\in_array($column, $this->optionalColumns, true)) {
                $ret[$column] = $this->columns[$column];
            }
        }

        return $ret;
    }
}
