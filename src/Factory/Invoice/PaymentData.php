<?php

/**
 * This is a convenience class for generating invoice payment data
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Factory
 * @author      Nails Dev Team
 */

namespace Nails\Invoice\Factory\Invoice;

/**
 * Class PaymentData
 *
 * @package Nails\Invoice\Factory\Invoice
 */
class PaymentData implements \JsonSerializable
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
