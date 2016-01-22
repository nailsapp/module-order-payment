<?php

/**
 * Complete Request model
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Model
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Invoice\Model;

use Nails\Factory;
use Nails\Invoice\Exception\CompleteRequestException;

class CompleteRequest
{
    protected $oDriver;
    protected $oPayment;

    // --------------------------------------------------------------------------

    /**
     * Set the driver to be used for the completion
     * @param string $sDriverSlug The driver's slug
     */
    public function setDriver($sDriverSlug)
    {
        //  Validate the driver
        $oPaymentDriverModel = Factory::model('PaymentDriver', 'nailsapp/module-invoice');
        $aDrivers            = $oPaymentDriverModel->getEnabled();
        $oDriver             = null;

        foreach ($aDrivers as $oDriverConfig) {
            if ($oDriverConfig->slug == $sDriverSlug) {
                $oDriver = $oPaymentDriverModel->getInstance($oDriverConfig->slug);
                break;
            }
        }

        if (empty($oDriver)) {
            throw new CompleteRequestException('"' . $sDriverSlug . '" is not a valid payment driver.', 1);
        }

        $this->oDriver = $oDriver;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the payment object
     * @param integer $iPaymentId The payment to complete
     */
    public function setPayment($iPaymentId)
    {
        //  Validate
        $oPaymentModel = Factory::model('Payment', 'nailsapp/module-invoice');
        $oPayment      = $oPaymentModel->getById($iPaymentId, array('includeInvoice' => true));

        if (empty($oPayment)) {
            throw new CompleteRequestException('Invalid payment ID.', 1);
        }

        $this->oPayment = $oPayment;
        return $this;
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
        //  Ensure we have a driver
        if (empty($this->oDriver)) {
            throw new CompleteRequestException('No driver selected.', 1);
        }

        //  Ensure we have a payment
        if (empty($this->oPayment)) {
            throw new CompleteRequestException('No payment selected.', 1);
        }

        //  Execute the completion
        $oCompleteResponse = $this->oDriver->complete(
            $aGetVars,
            $aPostVars
        );

        //  Validate driver response
        if (empty($oCompleteResponse)) {
            throw new CompleteRequestException('Response from driver was empty.', 1);
        }

        if (!($oCompleteResponse instanceof \Nails\Invoice\Model\CompleteResponse)) {
            throw new CompleteRequestException(
                'Response from driver must be an instance of \Nails\Invoice\Model\CompleteResponse.',
                1
            );
        }

        $oPaymentModel = Factory::model('Payment', 'nailsapp/module-invoice');
        $sPaymentClass = get_class($oPaymentModel);

        if ($oCompleteResponse->isOk()) {

            //  Update the payment
            $bResult = $oPaymentModel->update(
                $this->oPayment->id,
                array(
                    'status' => $sPaymentClass::STATUS_OK,
                    'txn_id' => $oCompleteResponse->getTxnId()
                )
            );

            if (empty($bResult)) {
                throw new CompleteRequestException('Failed to update existing payment.', 1);
            }

            //  Has the invoice been paid in full? If so, mark it as paid and fire the invoice.paid event
            $oInvoiceModel = Factory::model('Invoice', 'nailsapp/module-invoice');

            if ($oInvoiceModel->isPaid($this->oPayment->invoice->id)) {

                //  Mark Invoice as PAID
                if (!$oInvoiceModel->setPaid($this->oPayment->invoice->id)) {
                    throw new ChargeRequestException('Failed to mark invoice as paid.', 1);
                }

                //  Call back event
                $oPaymentEventHandler      = Factory::model('PaymentEventHandler', 'nailsapp/module-invoice');
                $sPaymentEventHandlerClass = get_class($oPaymentEventHandler);

                $oPaymentEventHandler->trigger(
                    $sPaymentEventHandlerClass::EVENT_INVOICE_PAID,
                    $oInvoiceModel->getById($this->oPayment->invoice->id, array('includeAll' => true))
                );

                //  Send receipt email
                $oInvoiceModel->sendReceipt($this->oPayment->invoice->id);
            }

        } elseif ($oCompleteResponse->isFail()) {

            //  Update the payment
            $bResult = $oPaymentModel->update(
                $oPayment->id,
                array(
                    'status'    => $sPaymentClass::STATUS_FAILED,
                    'fail_msg'  => $oCompleteResponse->getError()->msg,
                    'fail_code' => $oCompleteResponse->getError()->code
                )
            );

            if (empty($bResult)) {
                throw new CompleteRequestException('Failed to update existing payment.', 1);
            }
        }

        //  Lock the response so it cannot be altered
        $oCompleteResponse->lock();

        return $oCompleteResponse;
    }
}
