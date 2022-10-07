<?php

declare(strict_types=1);

namespace App\Models;

use App\Presenters\BasePresenter;
use Illuminate\Database\Eloquent\Model;

class Base extends Model
{
    protected BasePresenter $presenterInstance;

    protected string $presenter = BasePresenter::class;

    public static function getTableName(): string
    {
        return with(new static())->getTable();
    }

    /**
     * @return string[]
     */
    public static function getFillableColumns(): array
    {
        return with(new static())->getFillable();
    }

    public function present()
    {
        if (!$this->presenterInstance) {
            $this->presenterInstance = new $this->presenter($this);
        }

        return $this->presenterInstance;
    }

    /**
     * @return string[]
     */
    public function getEditableColumns(): array
    {
        return $this->fillable;
    }

    public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }

    public function getLocalizedColumn(string $key, string $locale = 'en'): string
    {
        if (empty($locale)) {
            $locale = 'en';
        }
        $localizedKey = $key.'_'.strtolower($locale);
        $value = $this->{$localizedKey};
        if (empty($value)) {
            $localizedKey = $key.'_en';
            $value = $this->{$localizedKey};
        }

        return $value;
    }

    public function toFillableArray(): array
    {
        $ret = [];
        foreach ($this->fillable as $key) {
            $ret[$key] = $this->{$key};
        }

        return $ret;
    }
}
