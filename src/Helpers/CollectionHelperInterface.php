<?php
namespace EnzanRocket\Foundation\Helpers;

interface CollectionHelperInterface
{
    /**
     * Set Locale.
     *
     * @param \Illuminate\Database\Eloquent\Collection $collection
     *
     * @return array
     */
    public function getSelectOptions($collection);
}
