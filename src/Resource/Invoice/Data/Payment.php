<?php

namespace Nails\Invoice\Resource\Invoice\Data;

use Nails\Common\Resource;

/**
 * Class Payment
 *
 * @package Nails\Invoice\Resource\Invoice
 */
class Payment extends Resource
{
    /**
     * Converts the object to a JSON string
     *
     * @return string
     */
    public function __toString(): string
    {
        return json_encode($this);
    }
}
