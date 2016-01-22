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
     * Initiate a payment
     * @param  integer   $iAmount      The payment amount
     * @param  string    $sCurrency    The payment currency
     * @param  array     $aData        An array of driver data
     * @param  string    $sDescription The charge description
     * @param  \stdClass $oPayment     The payment object
     * @param  \stdClass $oInvoice     The invoice object
     * @param  string    $sSuccessUrl  The URL to go to after successfull payment
     * @param  string    $sFailUrl     The URL to go to after failed payment
     * @return \Nails\Invoice\Model\ChargeResponse
     */
    public function charge(
        $iAmount,
        $sCurrency,
        $aData,
        $sDescription,
        $oPayment,
        $oInvoice,
        $sSuccessUrl,
        $sFailUrl
    )
    {
        throw new DriverException('Driver must implement the charge() method', 1);
    }

    // --------------------------------------------------------------------------

    /**
     * Complete the payment
     * @param  array $aGetVars  Any $_GET variables passed from the redirect flow
     * @param  array $aPostVars Any $_POST variables passed from the redirect flow
     * @return \Nails\Invoice\Model\CompleteResponse
     */
    public function complete($aGetVars, $aPostVars)
    {
        throw new DriverException('Driver must implement the complete() method', 1);
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
