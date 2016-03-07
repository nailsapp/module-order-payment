<?php

/**
 * Base Request Model
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Model
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Invoice\Model;

use Nails\Factory;
use Nails\Invoice\Exception\RequestException;

class RequestBase
{
    protected $oDriver;
    protected $oDriverModel;

    protected $oInvoice;
    protected $oInvoiceModel;

    protected $oPayment;
    protected $oPaymentModel;

    protected $oPaymentEventHandler;

    // --------------------------------------------------------------------------

    /**
     * Construct the request
     */
    public function __construct()
    {
        $this->oDriverModel         = Factory::model('PaymentDriver', 'nailsapp/module-invoice');
        $this->oInvoiceModel        = Factory::model('Invoice', 'nailsapp/module-invoice');
        $this->oPaymentModel        = Factory::model('Payment', 'nailsapp/module-invoice');
        $this->oPaymentEventHandler = Factory::model('PaymentEventHandler', 'nailsapp/module-invoice');
    }

    // --------------------------------------------------------------------------

    /**
     * Set the driver to be used for the request
     * @param string $sDriverSlug The driver's slug
     */
    public function setDriver($sDriverSlug)
    {
        //  Validate the driver
        $aDrivers = $this->oDriverModel->getEnabled();
        $oDriver  = null;

        foreach ($aDrivers as $oDriverConfig) {
            if ($oDriverConfig->slug == $sDriverSlug) {
                $oDriver = $this->oDriverModel->getInstance($oDriverConfig->slug);
                break;
            }
        }

        if (empty($oDriver)) {
            throw new RequestException('"' . $sDriverSlug . '" is not a valid payment driver.', 1);
        }

        $this->oDriver = $oDriver;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the invoice object
     * @param integer $iInvoiceId The invoice to use for the request
     */
    public function setInvoice($iInvoiceId)
    {
        //  Validate
        $oInvoice = $this->oInvoiceModel->getById($iInvoiceId, array('includeAll' => true));

        if (empty($oInvoice)) {
            throw new RequestException('Invalid invoice ID.', 1);
        }

        $this->oInvoice = $oInvoice;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the payment object
     * @param integer $iPaymentId The payment to use for the request
     */
    public function setPayment($iPaymentId)
    {
        //  Validate
        $oPayment = $this->oPaymentModel->getById($iPaymentId, array('includeInvoice' => true));

        if (empty($oPayment)) {
            throw new RequestException('Invalid payment ID.', 1);
        }

        $this->oPayment = $oPayment;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Set a payment as PROCESSING
     * @param string $sTxnId The payment's transaction ID
     */
    protected function setPaymentProcessing($sTxnId = null)
    {
        //  Ensure we have a payment
        if (empty($this->oPayment)) {
            throw new RequestException('No payment selected.', 1);
        }
        $sPaymentClass = get_class($this->oPaymentModel);
        $bResult       = $this->oPaymentModel->update(
            $this->oPayment->id,
            array(
                'status' => $sPaymentClass::STATUS_PROCESSING,
                'txn_id' => $sTxnId ? $sTxnId : null
            )
        );

        if (empty($bResult)) {
            throw new RequestException('Failed to update existing payment.', 1);
        }

        //  Has the invoice been paid in full? If so, mark it as paid and fire the invoice.paid.processing event
        if ($this->oInvoiceModel->isPaid($this->oInvoice->id, true)) {

            //  Mark Invoice as PAID_PROCESSING
            if (!$this->oInvoiceModel->setPaidProcessing($this->oInvoice->id)) {
                throw new RequestException('Failed to mark invoice as paid (processing).', 1);
            }
        }

        //  Send receipt email
        $this->oPaymentModel->sendReceipt($this->oPayment->id);
    }

    // --------------------------------------------------------------------------

    /**
     * Set a payment as COMPLETE, and mark the invoice as paid if so
     * @param string $sTxnId The payment's transaction ID
     */
    protected function setPaymentComplete($sTxnId = null)
    {
        //  Ensure we have a payment
        if (empty($this->oPayment)) {
            throw new RequestException('No payment selected.', 1);
        }

        //  Ensure we have an invoice
        if (empty($this->oInvoice)) {
            throw new RequestException('No invoice selected.', 1);
        }

        //  Update the payment
        $sPaymentClass = get_class($this->oPaymentModel);
        $bResult       = $this->oPaymentModel->update(
            $this->oPayment->id,
            array(
                'status' => $sPaymentClass::STATUS_COMPLETE,
                'txn_id' => $sTxnId ? $sTxnId : null
            )
        );

        if (empty($bResult)) {
            throw new RequestException('Failed to update existing payment.', 1);
        }

        //  Has the invoice been paid in full? If so, mark it as paid and fire the invoice.paid event
        if ($this->oInvoiceModel->isPaid($this->oInvoice->id)) {

            //  Mark Invoice as PAID
            if (!$this->oInvoiceModel->setPaid($this->oInvoice->id)) {
                throw new RequestException('Failed to mark invoice as paid.', 1);
            }
        }

        //  Send receipt email
        $this->oPaymentModel->sendReceipt($this->oPayment->id);
    }
}