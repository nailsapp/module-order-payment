<?php

/**
 * Base Request
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Factory
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Invoice\Factory;

use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\ModelException;
use Nails\Factory;
use Nails\Invoice\Exception\ChargeRequestException;
use Nails\Invoice\Exception\RequestException;

class RequestBase
{
    protected $oDriver;
    protected $oDriverService;
    protected $oInvoice;
    protected $oInvoiceModel;
    protected $oPayment;
    protected $oPaymentModel;
    protected $oRefund;
    protected $oRefundModel;

    // --------------------------------------------------------------------------

    /**
     * Construct the request
     */
    public function __construct()
    {
        $this->oDriverService = Factory::service('PaymentDriver', 'nails/module-invoice');
        $this->oInvoiceModel  = Factory::model('Invoice', 'nails/module-invoice');
        $this->oPaymentModel  = Factory::model('Payment', 'nails/module-invoice');
        $this->oRefundModel   = Factory::model('Refund', 'nails/module-invoice');
    }

    // --------------------------------------------------------------------------

    /**
     * Set the driver to be used for the request
     *
     * @param string $sDriverSlug The driver's slug
     *
     * @return $this
     * @throws RequestException
     */
    public function setDriver($sDriverSlug)
    {
        //  Validate the driver
        $aDrivers = $this->oDriverService->getEnabled();
        $oDriver  = null;

        foreach ($aDrivers as $oDriverConfig) {
            if ($oDriverConfig->slug == $sDriverSlug) {
                $oDriver = $this->oDriverService->getInstance($oDriverConfig->slug);
                break;
            }
        }

        if (empty($oDriver)) {
            throw new RequestException('"' . $sDriverSlug . '" is not a valid payment driver.');
        }

        $this->oDriver = $oDriver;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the invoice object
     *
     * @param integer $iInvoiceId The invoice to use for the request
     *
     * @return $this
     * @throws RequestException
     */
    public function setInvoice($iInvoiceId)
    {
        //  Validate
        $oModel   = $this->oInvoiceModel;
        $oInvoice = $oModel->getById(
            $iInvoiceId,
            ['expand' => $oModel::EXPAND_ALL]
        );

        if (empty($oInvoice)) {
            throw new RequestException('Invalid invoice ID.');
        }

        $this->oInvoice = $oInvoice;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the payment object
     *
     * @param integer $iPaymentId The payment to use for the request
     *
     * @return $this
     * @throws RequestException
     */
    public function setPayment($iPaymentId)
    {
        //  Validate
        $oPayment = $this->oPaymentModel->getById(
            $iPaymentId,
            ['expand' => ['invoice']]
        );

        if (empty($oPayment)) {
            throw new RequestException('Invalid payment ID.');
        }

        $this->oPayment = $oPayment;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the refund  object
     *
     * @param integer $iRefundId The refund to use for the request
     *
     * @return $this
     * @throws RequestException
     */
    public function setRefund($iRefundId)
    {
        //  Validate
        $oRefund = $this->oRefundModel->getById($iRefundId);

        if (empty($oRefund)) {
            throw new RequestException('Invalid refund ID.');
        }

        $this->oRefund = $oRefund;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Set a payment as PROCESSING
     *
     * @param string  $sTxnId The payment's transaction ID
     * @param integer $iFee   The fee charged by the processor, if known
     *
     * @return $this
     * @throws RequestException
     */
    protected function setPaymentProcessing($sTxnId = null, $iFee = null)
    {
        //  Ensure we have a payment
        if (empty($this->oPayment)) {
            throw new RequestException('No payment selected.');
        }

        //  Update the payment
        $aData = ['txn_id' => $sTxnId ? $sTxnId : null];

        if (!is_null($iFee)) {
            $aData['fee'] = $iFee;
        }

        if (!$this->oPaymentModel->setComplete($this->oPayment->id, $aData)) {
            throw new RequestException('Failed to update existing payment.');
        }

        //  Has the invoice been paid in full? If so, mark it as paid and fire the invoice.paid.processing event
        if ($this->oInvoiceModel->isPaid($this->oInvoice->id, true)) {

            //  Mark Invoice as PAID_PROCESSING
            if (!$this->oInvoiceModel->setPaidProcessing($this->oInvoice->id)) {
                throw new RequestException('Failed to mark invoice as paid (processing).');
            }
        }

        //  Send receipt email
        $this->oPaymentModel->sendReceipt($this->oPayment->id);
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Set a payment as COMPLETE, and mark the invoice as paid if so
     *
     * @param string  $sTxnId The payment's transaction ID
     * @param integer $iFee   The fee charged by the processor, if known
     *
     * @return $this
     * @throws RequestException
     */
    protected function setPaymentComplete($sTxnId = null, $iFee = null)
    {
        //  Ensure we have a payment
        if (empty($this->oPayment)) {
            throw new RequestException('No payment selected.');
        }

        //  Ensure we have an invoice
        if (empty($this->oInvoice)) {
            throw new RequestException('No invoice selected.');
        }

        //  Update the payment
        $aData = ['txn_id' => $sTxnId ? $sTxnId : null];

        if (!is_null($iFee)) {
            $aData['fee'] = $iFee;
        }

        if (!$this->oPaymentModel->setComplete($this->oPayment->id, $aData)) {
            throw new RequestException('Failed to update existing payment.');
        }

        //  Has the invoice been paid in full? If so, mark it as paid and fire the invoice.paid event
        if ($this->oInvoiceModel->isPaid($this->oInvoice->id)) {

            //  Mark Invoice as PAID
            if (!$this->oInvoiceModel->setPaid($this->oInvoice->id)) {
                throw new RequestException('Failed to mark invoice as paid.');
            }
        }

        //  Send receipt email
        $this->oPaymentModel->sendReceipt($this->oPayment->id);

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets a payment as failed
     *
     * @param string $sMessage The error message
     * @param string $sCode    The error code
     *
     * @throws ChargeRequestException
     * @throws FactoryException
     * @throws ModelException
     */
    protected function setPaymentFailed($sMessage, $sCode): void
    {
        //  Update the payment
        $sPaymentClass = get_class($this->oPaymentModel);
        $bResult       = $this->oPaymentModel->update(
            $this->oPayment->id,
            [
                'status'    => $sPaymentClass::STATUS_FAILED,
                'fail_msg'  => $sMessage,
                'fail_code' => $sCode,
            ]
        );

        if (empty($bResult)) {
            throw new ChargeRequestException('Failed to update existing payment.', 1);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Set a refund as COMPLETE
     *
     * @param string  $sTxnId       The refund's transaction ID
     * @param integer $iFeeRefunded The fee refunded by the processor, if known
     *
     * @return $this
     * @throws RequestException
     */
    protected function setRefundComplete($sTxnId = null, $iFeeRefunded = null)
    {
        //  Ensure we have a payment
        if (empty($this->oRefund)) {
            throw new RequestException('No refund selected.');
        }

        //  Update the refund
        $aData = ['txn_id' => $sTxnId ? $sTxnId : null];

        if (!is_null($iFeeRefunded)) {
            $aData['fee'] = $iFeeRefunded;
        }

        if (!$this->oRefundModel->setComplete($this->oRefund->id, $aData)) {
            throw new RequestException('Failed to update existing refund.');
        }

        // Update the associated payment, if the payment is fully refunded then mark it so
        $oPayment = $this->oPaymentModel->getById($this->oRefund->payment_id);
        if ($oPayment->available_for_refund->raw > 0) {
            $this->oPaymentModel->setRefundedPartial($oPayment->id);
        } else {
            $this->oPaymentModel->setRefunded($oPayment->id);
        }

        //  Send receipt email
        $this->oRefundModel->sendReceipt($this->oRefund->id);

        return $this;
    }
}
