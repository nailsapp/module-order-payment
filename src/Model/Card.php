<?php

/**
 * Tax rate model
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Model
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Invoice\Model;

use Nails\Factory;
use Nails\Invoice\Exception\CardException;

class Card
{
    protected $sToken;
    protected $sName;
    protected $sNumber;
    protected $sExpMonth;
    protected $sExpYear;
    protected $sCvc;

    // --------------------------------------------------------------------------

    /**
     * Set the card's token
     * @param string $sToken The card's token
     */
    public function setToken($sToken)
    {
        $this->sToken = $sToken;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the card's token
     * @return string The card's token
     */
    public function getToken()
    {
        return $this->sToken;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the cardholder's name
     * @param string $sName The cardholder's name
     */
    public function setName($sName)
    {
        $this->sName = $sName;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the cardholder's Name
     * @return string The cardholder's name
     */
    public function getName()
    {
        return $this->sName;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the card's number
     * @param string $sNumber The card's number
     */
    public function setNumber($sNumber)
    {
        //  Validate

        $this->sNumber = $sNumber;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the card's number
     * @return string The card's number
     */
    public function getNumber()
    {
        return $this->sNumber;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the card's expiry month
     * @param string $sExpiry The card's expiry month
     */
    public function setExpMonth($sExpMonth)
    {
        //  Validate
        $this->sExpMonth = $sExpMonth;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the card's expiry month
     * @return string The card's expiry month
     */
    public function getExpMonth()
    {
        return $this->sExpMonth;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the card's expiry year
     * @param string $sExpiry The card's expiry year
     */
    public function setExpYear($sExpYear)
    {
        //  Validate
        $this->sExpYear = $sExpYear;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the card's expiry year
     * @return string The card's expiry year
     */
    public function getExpYear()
    {
        return $this->sExpYear;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the card's CVC number
     * @param string $sCvc The card's cvc number
     */
    public function setCvc($sCvc)
    {
        //  Validate
        $this->sCvc = $sCvc;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the card's CVC number
     * @return string The card's CVC number
     */
    public function getCvc()
    {
        return $this->sCvc;
    }

    // --------------------------------------------------------------------------

    public function toArray()
    {
        return array(
            'token'     => $this->getToken(),
            'name'      => $this->getName(),
            'number'    => $this->getNumber(),
            'exp_month' => $this->getExpMonth(),
            'exp_year'  => $this->getExpYear(),
            'cvc'       => $this->getCvc()
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Attempts to charge the card
     * @param  integer   $iInvoiceId The invoice this charge should be attributed to
     * @param  integer   $iAmount    The amount to charge the card
     * @param  string    $sCurrency  The currency in which to charge
     * @param  string    $sDriver    The payment driver to use
     * @return \stdClass
     */
    public function charge($iInvoiceId, $iAmount, $sCurrency, $sDriver)
    {
        $oPaymentDriverModel = Factory::model('PaymentDriver', 'nailsapp/module-invoice');
        $aDrivers            = $oPaymentDriverModel->getEnabled();

        //  Validate the driver
        $oDriver = false;
        foreach ($aDrivers as $oDriverConfig) {
            if ($oDriverConfig->slug == $sDriver) {
                $oDriver = $oPaymentDriverModel->getInstance($oDriverConfig->slug);
                break;
            }
        }

        if (empty($oDriver)) {
            throw new CardException('"' . $sDriver . '" is not a valid payment driver.', 1);
        }

        // --------------------------------------------------------------------------

        //  Validate the invoice
        $oInvoiceModel = Factory::model('Invoice', 'nailsapp/module-invoice');
        $oInvoice      = $oInvoiceModel->getById($iInvoiceId);

        if (empty($oInvoice)) {
            throw new CardException('Invalid invoice ID.', 1);
        }

        // --------------------------------------------------------------------------

        if (!is_int($iAmount) || $iAmount <= 0) {
            throw new CardException('Amount must be a positive integer.', 1);
        }

        // --------------------------------------------------------------------------

        //  @todo: validate currency

        // --------------------------------------------------------------------------

        //  Create a charge against the invoice
        $oPaymentModel = Factory::model('Payment', 'nailsapp/module-invoice');
        $oPayment      = $oPaymentModel->create(
            array(
                'driver'        => $sDriver,
                'invoice_id'    => $iInvoiceId,
                'currency'      => $sCurrency,
                'amount'        => $iAmount,
            ),
            true
        );

        if (empty($oPayment)) {
            throw new CardException('Failed to create new payment.', 1);
        }

        //  Call the PaymentEventHandler
        $oPaymentEventHandler = Factory::model('PaymentEventHandler', 'nailsapp/module-invoice');
        $sPaymentClass        = get_class($oPaymentEventHandler);

        $oPaymentEventHandler->trigger($sPaymentClass::EVENT_PAYMENT_CREATED, $oPayment);

        $oResponse = $oDriver->charge(
            $this->toArray(),
            $iAmount,
            $sCurrency
        );

        //  Validate driver response
        if (empty($oResponse)) {
            throw new CardException('Response from driver was empty.', 1);
        }

        if (!($oResponse instanceof \Nails\Invoice\Model\ChargeResponse)) {
            throw new CardException(
                'Response from driver must be an instance of \Nails\Invoice\Model\ChargeResponse.',
                1
            );
        }

        //  Update the payment
        $bResult = $oPaymentModel->update(
            $oPayment->id,
            array(
                'status' => $oResponse->getStatus(),
                'txn_id' => $oResponse->getTxnId(),
                'fee'    => $oResponse->getFee()
            )
        );

        if (empty($bResult)) {
            throw new CardException('Failed to update existing payment.', 1);
        }

        $oPaymentEventHandler->trigger(
            $sPaymentClass::EVENT_PAYMENT_UPDATED,
            $oPaymentModel->getById($oPayment->id)
        );

        //  Has the invoice been paid in full? If so, mark it as paid and fire the invoice.paid event
        if ($oInvoice->totals->base->paid + $oPayment->amount->base >= $oInvoice->totals->base->grand) {

            //  Mark Invoice as PAID
            $oNow          = Factory::factory('DateTime');
            $sInvoiceClass = get_class($oInvoiceModel);
            $bResult       = $oInvoiceModel->update(
                $oInvoice->id,
                array(
                    'state' => $sInvoiceClass::STATE_PAID,
                    'paid'  => $oNow->format('Y-m-d')
                )
            );

            if (!$bResult) {
                throw new CardException('Failed to mark invoice as paid.', 1);
            }

            //  Call back event
            $oPaymentEventHandler->trigger(
                $sPaymentClass::EVENT_INVOICE_PAID,
                $oInvoiceModel->getById($oInvoice->id)
            );

            //  Send receipt email
            $oEmail        = new \stdClass();
            $oEmail->type  = 'invoice_paid_receipt';
            $oEmail->data  = new \stdClass();

            if (!empty($oInvoice->user_email)) {

                $aEmails = explode(',', $oInvoice->user_email);

            } elseif (!empty($oInvoice->user->email)) {

                $aEmails = array($oInvoice->user->email);

            } else {

                throw new CardException('No email address to send the invoice to', 1);
            }

            $oEmailer = Factory::service('Emailer', 'nailsapp/module-email');

            foreach ($aEmails as $sEmail) {
                $oEmail->to_email = $sEmail;
                $oResult = $oEmailer->send($oEmail);
            }
        }

        //  Lock the response so it cannot be altered
        $oResponse->lock();

        return $oResponse;
    }
}
