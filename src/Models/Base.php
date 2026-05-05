<?php
namespace EnzanRocket\Foundation\Models;

use Illuminate\Database\Eloquent\Model;
use EnzanRocket\Foundation\Presenters\BasePresenter;

class Base extends Model
{
    protected ?BasePresenter $presenterInstance = null;

    /** @var class-string<BasePresenter> */
    protected $presenter = BasePresenter::class;

    public static function getTableName(): string
    {
        return with(new static())->getTable();
    }

    /** @return string[] */
    public static function getFillableColumns(): array
    {
        return with(new static())->getFillable();
    }

    public function present(): BasePresenter
    {
        if (!$this->presenterInstance) {
            $this->presenterInstance = new $this->presenter($this);
        }

        return $this->presenterInstance;
    }

    /** @return string[] */
    public function getEditableColumns(): array
    {
        return $this->fillable;
    }

    public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }

    public function getLocalizedColumn(string $key, string $locale = 'en'): mixed
    {
        if (empty($locale)) {
            $locale = 'en';
        }
        $localizedKey = $key.'_'.strtolower($locale);
        $value        = $this->$localizedKey;
        if (empty($value)) {
            $localizedKey = $key.'_en';
            $value        = $this->$localizedKey;
        }

        return $value;
    }

    /** @return array<string, mixed> */
    public function toFillableArray(): array
    {
        $ret = [];
        foreach ($this->fillable as $key) {
            $ret[$key] = $this->$key;
        }

        return $ret;
    }

    // REMOVED: getDateColumns() — accessed $this->dates which was removed in Laravel 10.
    // Confirmed: no callers in any child model or service.
}
