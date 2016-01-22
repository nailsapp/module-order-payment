<?php

/**
 * Attempts a charge
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Model
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Invoice\Model;

use Nails\Factory;
use Nails\Invoice\Exception\ChargeRequestException;

class ChargeRequest
{
    protected $oDriver;
    protected $oCard;
    protected $oCustom;

    // --------------------------------------------------------------------------

    /**
     * Cosntruct the charge request
     */
    public function __construct()
    {
        //  Card details
        $this->oCard             = new \stdClass();
        $this->oCard->name       = null;
        $this->oCard->number     = null;
        $this->oCard->exp        = new \stdClass();
        $this->oCard->exp->month = null;
        $this->oCard->exp->year  = null;
        $this->oCard->cvc        = null;

        //  Container for custom variables
        $this->oCustom = new \stdClass();
    }

    // --------------------------------------------------------------------------

    /**
     * Set the driver to be used for the charge
     * @param string $sDriverSlug The driver's slug
     */
    public function setDriver($sDriverSlug)
    {
        $oPaymentDriverModel = Factory::model('PaymentDriver', 'nailsapp/module-invoice');
        $aDrivers            = $oPaymentDriverModel->getEnabled();

        //  Validate the driver
        $oDriver = null;
        foreach ($aDrivers as $oDriverConfig) {
            if ($oDriverConfig->slug == $sDriverSlug) {
                $oDriver = $oPaymentDriverModel->getInstance($oDriverConfig->slug);
                break;
            }
        }

        if (empty($oDriver)) {
            throw new ChargeRequestException('"' . $sDriver . '" is not a valid payment driver.', 1);
        }

        $this->oDriver = $oDriver;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the cardholder's name
     * @param string $sCardName The cardholder's name
     */
    public function setCardName($sCardName)
    {
        $this->oCard->name = $sCardName;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the cardholder's Name
     * @return string The cardholder's name
     */
    public function getCardName()
    {
        return $this->oCard->name;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the card's number
     * @param string $sCardNumber The card's number
     */
    public function setCardNumber($sCardNumber)
    {
        //  Validate
        if (preg_match('/[^\d ]/', $sCardNumber)) {
            throw new ChargeRequestException('Invalid card number; can only contain digits and spaces.', 1);

        }
        $this->oCard->number = $sCardNumber;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the card's number
     * @return string The card's number
     */
    public function getCardNumber()
    {
        return $this->oCard->number;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the card's expiry month
     * @param string $sExpiry The card's expiry month
     */
    public function setCardExpMonth($sCardExpMonth)
    {
        //  Validate
        if (is_numeric($sCardExpMonth)) {

            $iMonth = (int) $sCardExpMonth;
            if ($iMonth < 1 || $iMonth >  12) {

                throw new ChargeRequestException('Invalid Expiry Month; must be in the range 1-12.', 1);

            } else {

                $this->oCard->exp->month = $iMonth < 10 ? '0' . $iMonth : (string) $iMonth;
                return $this;
            }

        } else {

            throw new ChargeRequestException('Invalid Expiry Month; must be numeric.', 1);
        }

        $this->oCard->exp->month = $sCardExpMonth;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the card's expiry month
     * @return string The card's expiry month
     */
    public function getCardExpMonth()
    {
        return $this->oCard->exp->month;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the card's expiry year
     * @param string $sExpiry The card's expiry year
     */
    public function setCardExpYear($sCardExpYear)
    {
        //  Validate
        if (is_numeric($sCardExpYear)) {

            //  Accept two digits or 4 digits only
            if (strlen($sCardExpYear) == 2 || strlen($sCardExpYear) == 4) {

                //  Two digit values should be turned into a 4 digit value
                if (strlen($sCardExpYear) == 2) {

                    //  Sorry people living in the 2100's, I'm very sorry everything is broken.
                    $sCardExpYear = '20' . $sCardExpYear;
                }

                $iYear = (int) $sCardExpYear;
                $oNow  = Factory::factory('DateTime');

                if ($oNow->format('Y') > $iYear) {
                    throw new ChargeRequestException(
                        'Invalid Expiry Year; must ' . $oNow->format('Y') . ' be or later.',
                        1
                    );
                }

                $this->oCard->exp->year = (string) $iYear;
                return $this;

            } else {

                throw new ChargeRequestException('Invalid Expiry Year; must be 2 or 4 digits.', 1);
            }

        } else {

            throw new ChargeRequestException('Invalid Expiry Month; must be numeric.', 1);
        }

        $this->oCard->exp->year = $sCardExpYear;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the card's expiry year
     * @return string The card's expiry year
     */
    public function getCardExpYear()
    {
        return $this->oCard->exp->year;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the card's CVC number
     * @param string $sCardCvc The card's cvc number
     */
    public function setCardCvc($sCardCvc)
    {
        //  Validate
        $this->oCard->cvc = $sCardCvc;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the card's CVC number
     * @return string The card's CVC number
     */
    public function getCardCvc()
    {
        return $this->oCard->cvc;
    }

    // --------------------------------------------------------------------------

    /**
     * Set a custom value
     * @param string $sProperty   The property tos et
     * @param mixed  $mValue      The value to set
     */
    public function setCustom($sProperty, $mValue)
    {
        $this->oCustom->{$sProperty} = $mValue;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Retrieve a custom value
     * @param  string $sProperty The property to retrieve
     * @return mixed
     */
    public function getCustom($sProperty)
    {
        return property_exists($this->oCustom, $sProperty) ? $this->oCustom->{$sProperty} : null;
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
        //  Ensure we have a driver
        if (empty($this->oDriver)) {
            throw new ChargeRequestException('No driver selected.', 1);
        }

        //  Validate the invoice
        $oInvoiceModel = Factory::model('Invoice', 'nailsapp/module-invoice');
        $oInvoice      = $oInvoiceModel->getById($iInvoiceId);

        if (empty($oInvoice)) {
            throw new ChargeRequestException('Invalid invoice ID.', 1);
        }

        // --------------------------------------------------------------------------

        if (!is_int($iAmount) || $iAmount <= 0) {
            throw new ChargeRequestException('Amount must be a positive integer.', 1);
        }

        // --------------------------------------------------------------------------

        //  @todo: validate currency

        // --------------------------------------------------------------------------

        //  Create a charge against the invoice
        $oPaymentModel = Factory::model('Payment', 'nailsapp/module-invoice');
        $oPayment      = $oPaymentModel->create(
            array(
                'driver'     => $sDriver,
                'invoice_id' => $iInvoiceId,
                'currency'   => $sCurrency,
                'amount'     => $iAmount,
            ),
            true
        );

        if (empty($oPayment)) {
            throw new ChargeRequestException('Failed to create new payment.', 1);
        }

        $aDriverData = array();
        $mFields     = $this->oDriver->getPaymentFields();

        if (!empty($mFields) && $mFields == 'CARD') {

            $aDriverData = $this->oCard;

        } elseif (!empty($mFields)) {

            $aDriverData = $this->oCustom;
        }

        //  Return URL for drivers which implement a redirect flow
        $sReturnUrl = site_url('invoice/payment/processing/' . $oPayment->id . '/' . $oPayment->token);
        $oResponse  = $this->oDriver->charge(
            $aDriverData,
            $iAmount,
            $sCurrency,
            $sReturnUrl
        );

        //  Validate driver response
        if (empty($oResponse)) {
            throw new ChargeRequestException('Response from driver was empty.', 1);
        }

        if (!($oResponse instanceof \Nails\Invoice\Model\ChargeResponse)) {
            throw new ChargeRequestException(
                'Response from driver must be an instance of \Nails\Invoice\Model\ChargeResponse.',
                1
            );
        }

        /**
         * If we need to do a redirect then handle it; if the payment is OK, i.e cleared, then
         * update the things and send the receipts.
         */

        if ($oResponse->isRedirect()) {

            $sRedirectUrl = $oResponse->getRedirectUrl();
            $aPostData    = $oResponse->getRedirectPostData();

            if (empty($aPostData)) {

                redirect($sRedirectUrl);

            } else {

                $oCi = get_instance();
                echo $oCi->load->view(
                    'invoice/pay/post',
                    array(
                        'redirectUrl' => $sRedirectUrl,
                        'postFields'  => $aPostData
                    ),
                    true
                );
                exit();
            }

        } elseif ($oResponse->isOk()) {

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
                throw new ChargeRequestException('Failed to update existing payment.', 1);
            }

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
                    throw new ChargeRequestException('Failed to mark invoice as paid.', 1);
                }

                //  Call back event
                $oPaymentEventHandler = Factory::model('PaymentEventHandler', 'nailsapp/module-invoice');
                $sPaymentClass        = get_class($oPaymentEventHandler);

                $oPaymentEventHandler->trigger(
                    $sPaymentClass::EVENT_INVOICE_PAID,
                    $oInvoiceModel->getById($oInvoice->id)
                );

                //  Send receipt email
                $oEmail       = new \stdClass();
                $oEmail->type = 'invoice_paid_receipt';
                $oEmail->data = new \stdClass();

                if (!empty($oInvoice->user_email)) {

                    $aEmails = explode(',', $oInvoice->user_email);

                } elseif (!empty($oInvoice->user->email)) {

                    $aEmails = array($oInvoice->user->email);

                } else {

                    throw new ChargeRequestException('No email address to send the invoice to.', 1);
                }

                $oEmailer           = Factory::service('Emailer', 'nailsapp/module-email');
                $oInvoiceEmailModel = Factory::model('InvoiceEmail', 'nailsapp/module-invoice');

                foreach ($aEmails as $sEmail) {

                    $oEmail->to_email = $sEmail;
                    $oResult = $oEmailer->send($oEmail);

                    if (!empty($oResult)) {

                        $oInvoiceEmailModel->create(
                            array(
                                'invoice_id' => $oInvoice->id,
                                'email_id'   => $oResult->id,
                                'email_type' => $oEmail->type,
                                'recipient'  => $oEmail->to_email
                            )
                        );

                    } else {

                        throw new ChargeRequestException($oEmailer->lastError(), 1);
                    }
                }
            }
        }

        //  Set the success URL if it's currently blank
        if (empty($oResponse->getSuccessUrl())) {
            $oResponse->setSuccessUrl($sReturnUrl);
        }

        //  Lock the response so it cannot be altered
        $oResponse->lock();

        return $oResponse;
    }
}
