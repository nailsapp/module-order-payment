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
    const PAYMENT_FIELDS_CARD = 'CARD';

    // --------------------------------------------------------------------------

    /**
     * Returns whether the driver uses a redirect payment flow or not.
     * @return boolean
     */
    public function isRedirect()
    {
        throw new DriverException('Driver must implement the isRedirect() method', 1);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the payment fields the driver requires, use self::PAYMENT_FIELDS_CARD
     * for basic credit card details.
     * @return mixed
     */
    public function paymentFields()
    {
        throw new DriverException('Driver must implement the paymentFields() method', 1);
    }

    // --------------------------------------------------------------------------

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
