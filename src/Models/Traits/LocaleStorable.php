<?php
namespace EnzanRocket\Foundation\Models\Traits;

/**
 * EnzanRocket\Foundation\Models\LocaleStorable.
 *
 * @property string $locale
 *
 * @method static \Illuminate\Database\Query\Builder|\EnzanRocket\Foundation\Models\Traits\LocaleStorable whereLocale($value)
 */
trait LocaleStorable
{
    public function getLocale()
    {
        return $this->locale;
    }

    public function setLocale($locale)
    {
        $this->locale = strtolower($locale);
        $this->save();
    }
}
