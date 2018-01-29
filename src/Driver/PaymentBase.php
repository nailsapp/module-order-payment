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

use Nails\Common\Driver\Base;
use Nails\Invoice\Exception\DriverException;

class PaymentBase extends Base
{
    /**
     * Shortcut for requiring basic card details
     * @var string
     */
    const PAYMENT_FIELDS_CARD = 'CARD';

    // --------------------------------------------------------------------------

    /**
     * Returns whether the driver is available to be used against the selected invoice
     *
     * @param \stdClass $oInvoice The invoice being charged
     *
     * @throws DriverException
     * @return boolean
     */
    public function isAvailable($oInvoice)
    {
        throw new DriverException('Driver must implement the isAvailable() method', 1);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns whether the driver uses a redirect payment flow or not.
     *
     * @throws DriverException
     * @return boolean
     */
    public function isRedirect()
    {
        throw new DriverException('Driver must implement the isRedirect() method', 1);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the payment fields the driver requires, use static::PAYMENT_FIELDS_CARD
     * for basic credit card details.
     *
     * @throws DriverException
     * @return mixed
     */
    public function getPaymentFields()
    {
        throw new DriverException('Driver must implement the getPaymentFields() method', 1);
    }

    // --------------------------------------------------------------------------

    /**
     * Initiate a payment
     *
     * @param  integer   $iAmount      The payment amount
     * @param  string    $sCurrency    The payment currency
     * @param  \stdClass $oData        An array of driver data
     * @param  \stdClass $oCustomData  The custom data object
     * @param  string    $sDescription The charge description
     * @param  \stdClass $oPayment     The payment object
     * @param  \stdClass $oInvoice     The invoice object
     * @param  string    $sSuccessUrl  The URL to go to after successful payment
     * @param  string    $sFailUrl     The URL to go to after failed payment
     * @param  string    $sContinueUrl The URL to go to after payment is completed
     *
     * @throws DriverException
     * @return \Nails\Invoice\Model\ChargeResponse
     */
    public function charge(
        $iAmount,
        $sCurrency,
        $oData,
        $oCustomData,
        $sDescription,
        $oPayment,
        $oInvoice,
        $sSuccessUrl,
        $sFailUrl,
        $sContinueUrl
    ) {
        throw new DriverException('Driver must implement the charge() method', 1);
    }

// --------------------------------------------------------------------------

    /**
     * Complete the payment
     *
     * @param  \stdClass $oPayment  The Payment object
     * @param  \stdClass $oInvoice  The Invoice object
     * @param  array     $aGetVars  Any $_GET variables passed from the redirect flow
     * @param  array     $aPostVars Any $_POST variables passed from the redirect flow
     *
     * @throws DriverException
     * @return \Nails\Invoice\Model\CompleteResponse
     */
    public function complete($oPayment, $oInvoice, $aGetVars, $aPostVars)
    {
        throw new DriverException('Driver must implement the complete() method', 1);
    }

// --------------------------------------------------------------------------

    /**
     * Issue a refund for a payment
     *
     * @param  string    $sTxnId      The transaction's ID
     * @param  integer   $iAmount     The amount to refund
     * @param  string    $sCurrency   The currency in which to refund
     * @param  \stdClass $oCustomData The custom data object
     * @param  string    $sReason     The refund's reason
     * @param  \stdClass $oPayment    The payment object
     * @param  \stdClass $oInvoice    The invoice object
     *
     * @throws DriverException
     * @return \Nails\Invoice\Model\RefundResponse
     */
    public function refund($sTxnId, $iAmount, $sCurrency, $oCustomData, $sReason, $oPayment, $oInvoice)
    {
        throw new DriverException('Driver must implement the refund() method', 1);
    }
}
