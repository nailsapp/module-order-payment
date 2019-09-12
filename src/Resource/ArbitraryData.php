<?php

/**
 * This class represents arbitrary data objects
 *
 * @package  Nails\Invoice\Resource
 * @category resource
 */

namespace Nails\Invoice\Resource;

use Nails\Common\Resource;

/**
 * Class ArbitraryData
 *
 * @package Nails\Invoice\Resource
 */
abstract class ArbitraryData extends Resource
{
    /**
     * Format the object to JSON
     *
     * @return string
     */
    public function __toString(): string
    {
        return json_encode($this);
    }
}
