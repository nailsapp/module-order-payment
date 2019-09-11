<?php

namespace Nails\Invoice\Resource\Invoice\Data;

use Nails\Common\Resource;

/**
 * Class Callback
 *
 * @package Nails\Invoice\Resource\Invoice
 */
class Callback extends Resource
{
    public function __toString(): string
    {
        return json_encode($this);
    }
}
