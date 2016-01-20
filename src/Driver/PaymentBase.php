<?php

/**
 * Payment driver base
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Interface
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Invoice\Driver;

use \Nails\Common\Driver\Base;
use Nails\Invoice\Exception\DriverException;

class PaymentBase extends Base
{
    /**
     * Take a payment
     * @return boolean
     */
    public function charge()
    {
        throw new DriverException('Driver must implement the charge() method', 1);
    }

    // --------------------------------------------------------------------------

    /**
     * Issue a refund for a payment
     * @return boolean
     */
    public function refund()
    {
        throw new DriverException('Driver must implement the refund() method', 1);
    }
}
