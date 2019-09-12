<?php

namespace Nails\Invoice\Resource\Invoice\Item;

use Nails\Common\Resource;

/**
 * Class Unit
 *
 * @package Nails\Invoice\Resource\Invoice\Item
 */
class Unit extends Resource
{
    /**
     * The unit's ID
     *
     * @var string
     */
    public $id;

    /**
     * The unit's label
     *
     * @var string
     */
    public $label;
}
