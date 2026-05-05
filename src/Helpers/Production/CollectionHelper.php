<?php
namespace EnzanRocket\Foundation\Helpers\Production;

use EnzanRocket\Foundation\Helpers\CollectionHelperInterface;

class CollectionHelper implements CollectionHelperInterface
{
    public function getSelectOptions($collection)
    {
        return $collection->pluck('name', 'id')->toArray();
    }
}
