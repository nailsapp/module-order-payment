<?php

/**
 * This is a convenience class for generating invoice callback data
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Factory
 * @author      Nails Dev Team
 */

namespace Nails\Invoice\Factory\Invoice;

/**
 * Class CallbackData
 *
 * @package Nails\Invoice\Factory\Invoice
 */
class CallbackData implements \JsonSerializable
{
    /**
     * Returns public properties for serialisation
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
