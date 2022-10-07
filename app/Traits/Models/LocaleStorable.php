<?php

declare(strict_types=1);

namespace App\Traits\Models;

/**
 * App\Traits\Models,.
 *
 * @property string $locale
 *
 * @method static \Illuminate\Database\Query\Builder whereLocale($value)
 */
trait LocaleStorable
{
    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale($locale): void
    {
        $this->locale = strtolower($locale);
        $this->save();
    }
}
