<?php
namespace EnzanRocket\Foundation\Presenters;

class BasePresenter
{
    protected mixed $entity;

    protected string $toStringColumn = '';

    /** @var string[] */
    protected array $multilingualFields = [];

    public function __construct(mixed $entity)
    {
        $this->entity = $entity;
    }

    /**
     * Property access priority:
     * 1. If a method with the same name exists on this Presenter, call it.
     *    e.g. $presenter->type  →  calls $this->type()
     * 2. If the property name is listed in $multilingualFields, delegate to
     *    $this->entity->getLocalizedColumn($property).
     * 3. Otherwise proxy directly to $this->entity->$property (Eloquent attribute).
     *    No explicit fallback — missing attributes return null via Eloquent magic.
     */
    public function __get(string $property): mixed
    {
        if (method_exists($this, $property)) {
            return $this->$property();
        }

        if (in_array($property, $this->multilingualFields, strict: true)) {
            return $this->entity->getLocalizedColumn($property);
        }

        return $this->entity->$property;
    }

    public function toString(): mixed
    {
        $column = $this->toStringColumn;

        $value = $this->entity->$column;
        if (!empty($value)) {
            return $value;
        }

        return $this->entity->name;
    }
}
