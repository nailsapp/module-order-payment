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
use Nails\Invoice\Constants;
use Nails\Invoice\Exception\ChargeRequestException;
use Nails\Invoice\Exception\RequestException;
use Nails\Invoice\Interfaces\Driver\Payment;
use Nails\Invoice\Model;
use Nails\Invoice\Resource;
use Nails\Invoice\Service\PaymentDriver;

/**
 * Class RequestBase
 *
 * @package Nails\Invoice\Factory
 */
class RequestBase
{
    /**
     * The payment driver instance
     *
     * @var Payment
     */
    protected $oDriver;

    /**
     * The Payment Driver service
     *
     * @var PaymentDriver
     */
    protected $oDriverService;

    /**
     * The Invoice object
     *
     * @var Resource\Invoice
     */
    protected $oInvoice;

    /**
     * The Invoice model
     *
     * @var Model\Invoice
     */
    protected $oInvoiceModel;

    /**
     * The payment object
     *
     * @var Resource\Payment
     */
    protected $oPayment;

    /**
     * The Payment model
     *
     * @var Model\Payment
     */
    protected $oPaymentModel;

    /**
     * The source object
     *
     * @var Resource\Source
     */
    protected $oSource;

    /**
     * The Source model
     *
     * @var Model\Source
     */
    protected $oSourceModel;

    /**
     * The Refund object
     *
     * @var Resource\Refund
     */
    protected $oRefund;

    /**
     * The Refund model
     *
     * @var Model\Refund
     */
    protected $oRefundModel;

    /**
     * The URL to redirect to when successfull
     *
     * @var string
     */
    protected $sSuccessUrl = '';

    /**
     * The URL to redirect to in event of an error
     *
     * @var string
     */
    protected $sErrorUrl = '';

    /**
     * The URL to redirect to in event of user cancelation
     *
     * @var string
     */
    protected $sCancelUrl = '';

    // --------------------------------------------------------------------------

    /**
     * RequestBase constructor.
     *
     * @throws FactoryException
     */
    public function __construct()
    {
        $this->oDriverService = Factory::service('PaymentDriver', Constants::MODULE_SLUG);
        $this->oInvoiceModel  = Factory::model('Invoice', Constants::MODULE_SLUG);
        $this->oPaymentModel  = Factory::model('Payment', Constants::MODULE_SLUG);
        $this->oRefundModel   = Factory::model('Refund', Constants::MODULE_SLUG);
    }

    // --------------------------------------------------------------------------

    /**
     * Set the driver to be used for the request
     *
     * @param string|Payment $mDriver A driver object or slug
     *
     * @return $this
     * @throws RequestException
     */
    public function setDriver($mDriver)
    {
        if (!($mDriver instanceof Payment)) {

            $aDrivers = $this->oDriverService->getEnabled();
            $oDriver  = null;

            foreach ($aDrivers as $oDriverConfig) {
                if ($oDriverConfig->slug == $mDriver) {
                    $oDriver = $this->oDriverService->getInstance($oDriverConfig->slug);
                    break;
                }
            }

            if (empty($oDriver)) {
                throw new RequestException('"' . $mDriver . '" is not a valid payment driver.');
            }
        }

        $this->oDriver = $oDriver;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the payment driver
     *
     * @return Payment|null
     */
    public function getDriver(): ?Payment
    {
        return $this->oDriver;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the invoice object
     *
     * @param Resource\Invoice|int $mInvoice The invoice to use for the request
     *
     * @return $this
     * @throws RequestException
     */
    public function setInvoice($mInvoice)
    {
        if (!($mInvoice instanceof Resource\Invoice)) {

            $oModel   = $this->oInvoiceModel;
            $oInvoice = $oModel->getById(
                $mInvoice,
                ['expand' => $oModel::EXPAND_ALL]
            );

            if (empty($oInvoice)) {
                throw new RequestException('Invalid invoice ID.');
            }
        }

        $this->oInvoice = $oInvoice;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the Invoice object
     *
     * @return Resource\Invoice|null
     */
    public function getInvoice(): ?Resource\Invoice
    {
        return $this->oInvoice;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the payment object
     *
     * @param Resource\Payment|int $mPayment The payment to use for the request
     *
     * @return $this
     * @throws RequestException
     */
    public function setPayment($mPayment)
    {
        if (!($mPayment instanceof Resource\Payment)) {

            $oPayment = $this->oPaymentModel->getById(
                $mPayment,
                ['expand' => ['invoice']]
            );

            if (empty($oPayment)) {
                throw new RequestException('Invalid payment ID.');
            }
        }

        $this->oPayment = $oPayment;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the Payment object
     *
     * @return Resource\Payment|null
     */
    public function getPayment(): ?Resource\Payment
    {
        return $this->oPayment;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the source object
     *
     * @param Resource\Source|int $mSource The source to use for the request
     *
     * @return $this
     * @throws RequestException
     */
    public function setSource($mSource)
    {
        if (!($mSource instanceof Resource\Source)) {

            $oSource = $this->oSourceModel->getById($mSource);

            if (empty($oSource)) {
                throw new RequestException('Invalid source ID.');
            }
        }

        $this->oSource = $oSource;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the Source object
     *
     * @return Resource\Source|null
     */
    public function getSource(): ?Resource\Source
    {
        return $this->oSource;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the refund  object
     *
     * @param Resource\Refund|int $mRefund The refund to use for the request
     *
     * @return $this
     * @throws RequestException
     */
    public function setRefund($mRefund)
    {
        if (!($mRefund instanceof Resource\Refund)) {

            $oRefund = $this->oRefundModel->getById($mRefund);

            if (empty($oRefund)) {
                throw new RequestException('Invalid refund ID.');
            }
        }

        $this->oRefund = $oRefund;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the Refund object
     *
     * @return Resource\Refund|null
     */
    public function getRefund(): ?Resource\Refund
    {
        return $this->oRefund;
    }

    // --------------------------------------------------------------------------

    /**
     * Set a payment as PROCESSING
     *
     * @param string $sTxnId The payment's transaction ID
     * @param int    $iFee   The fee charged by the processor, if known
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
     * @param string $sTxnId The payment's transaction ID
     * @param int    $iFee   The fee charged by the processor, if known
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
     * @param string $sTxnId       The refund's transaction ID
     * @param int    $iFeeRefunded The fee refunded by the processor, if known
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

    // --------------------------------------------------------------------------

    /**
     * Set the success URL
     *
     * @param string $sSuccessUrl The success URL
     *
     * @return $this
     */
    public function setSuccessUrl(string $sSuccessUrl): RequestBase
    {
        $this->sSuccessUrl = $sSuccessUrl;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the success URL
     *
     * @return string
     */
    public function getSuccessUrl(): string
    {
        return $this->sSuccessUrl;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the error URL
     *
     * @param string $sErrorUrl The the error URL
     *
     * @return $this
     */
    public function setErrorUrl(string $sErrorUrl): RequestBase
    {
        $this->sErrorUrl = $sErrorUrl;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the error URL
     *
     * @return string
     */
    public function getErrorUrl(): string
    {
        return $this->sErrorUrl;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the cancel URL
     *
     * @param string $sCancelUrl The the cancel URL
     *
     * @return $this
     */
    public function setCancelUrl(string $sCancelUrl): RequestBase
    {
        $this->sCancelUrl = $sCancelUrl;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the cancel URL
     *
     * @return string
     */
    public function getCancelUrl(): string
    {
        return $this->sCancelUrl;
    }
}
