<?php

namespace Nails\Invoice\Resource\Payment\Data;

use Nails\Common\Resource;

/**
 * Class Status
 *
 * @package Nails\Invoice\Resource\Payment\Data
 */
class Sca extends Resource
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
