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
     * Returns whether the driver uses a redirect payment flow or not.
     * @return boolean
     */
    public function isRedirect()
    {
        throw new DriverException('Driver must implement the isRedirect() method', 1);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns any data which should be POSTED to the endpoint as part of a redirect
     * flow; if empty a header redirect is used instead.
     * @return array
     */
    public function getRedirectPostData()
    {
        throw new DriverException('Driver must implement the getRedirectPostData() method', 1);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the payment fields the driver requires, use self::PAYMENT_FIELDS_CARD
     * for basic credit card details.
     * @return mixed
     */
    public function getPaymentFields()
    {
        throw new DriverException('Driver must implement the getPaymentFields() method', 1);
    }

    // --------------------------------------------------------------------------

    /**
     * Take a payment
     * @param  array   $aData      Any data to use for processing the transaction, e.g., card details
     * @param  integer $iAmount    The amount to charge
     * @param  string  $sCurrency  The currency to charge in
     * @param  string  $sReturnUrl The return URL (if redirecting)
     * @return \Nails\Invoice\Model\ChargeResponse
     */
    public function charge($aData, $iAmount, $sCurrency, $sReturnUrl)
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
