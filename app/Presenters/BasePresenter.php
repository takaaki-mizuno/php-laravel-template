<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Models\Base;

class BasePresenter
{
    protected Base $entity;

    protected string $toStringColumn = '';

    /**
     * @var string[]
     */
    protected array $multilingualFields = [];

    public function __construct(Base $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return mixed
     */
    public function __get(string $property)
    {
        if (method_exists($this, $property)) {
            return $this->{$property}();
        }

        if (\in_array($property, $this->multilingualFields, true)) {
            return $this->entity->getLocalizedColumn($property);
        }

        return $this->entity->{$property};
    }

    public function toString(): string
    {
        $column = $this->toStringColumn;

        $value = $this->entity->{$column};
        if (!empty($value)) {
            return $value;
        }

        return $this->entity->name;
    }
}
