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

class ChargeRequest extends RequestBase
{
    protected $oCard;
    protected $oCustomField;
    protected $oCustomData;
    protected $sDescription;
    protected $bAutoRedirect;
    protected $sContinueUrl;

    // --------------------------------------------------------------------------

    /**
     * ChargeRequest constructor.
     */
    public function __construct()
    {
        parent::__construct();

        //  Card details
        $this->oCard             = new \stdClass();
        $this->oCard->name       = null;
        $this->oCard->number     = null;
        $this->oCard->exp        = new \stdClass();
        $this->oCard->exp->month = null;
        $this->oCard->exp->year  = null;
        $this->oCard->cvc        = null;

        //  Container for custom fields and data
        $this->oCustomField = new \stdClass();
        $this->oCustomData  = new \stdClass();

        //  Auto redirect by default
        $this->bAutoRedirect = true;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the cardholder's name
     *
     * @param string $sCardName The cardholder's name
     *
     * @return $this
     */
    public function setCardName($sCardName)
    {
        $this->oCard->name = $sCardName;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the cardholder's Name
     * @return string
     */
    public function getCardName()
    {
        return $this->oCard->name;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the card's number
     *
     * @param string $sCardNumber The card's number
     *
     * @throws ChargeRequestException
     * @return $this
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
     * @return string
     */
    public function getCardNumber()
    {
        return $this->oCard->number;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the card's expiry month
     *
     * @param string $sCardExpMonth The card's expiry month
     *
     * @throws ChargeRequestException
     * @return $this
     */
    public function setCardExpMonth($sCardExpMonth)
    {
        //  Validate
        if (is_numeric($sCardExpMonth)) {

            $iMonth = (int) $sCardExpMonth;
            if ($iMonth < 1 || $iMonth > 12) {

                throw new ChargeRequestException(
                    '"' . $sCardExpMonth . '" is an invalid expiry month; must be in the range 1-12.',
                    1
                );

            } else {

                $this->oCard->exp->month = $iMonth < 10 ? '0' . $iMonth : (string) $iMonth;
                return $this;
            }

        } else {

            throw new ChargeRequestException(
                '"' . $sCardExpMonth . '" is an invalid expiry month; must be numeric.',
                1
            );
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Get the card's expiry month
     * @return string
     */
    public function getCardExpMonth()
    {
        return $this->oCard->exp->month;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the card's expiry year
     *
     * @param string $sCardExpYear The card's expiry year
     *
     * @throws ChargeRequestException
     * @return $this
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
                        '"' . $sCardExpYear . '" is an invalid expiry year; must be ' . $oNow->format('Y') . ' or later.',
                        1
                    );
                }

                $this->oCard->exp->year = (string) $iYear;
                return $this;

            } else {

                throw new ChargeRequestException(
                    '"' . $sCardExpYear . '" is an invalid expiry year; must be 2 or 4 digits.',
                    1
                );
            }

        } else {

            throw new ChargeRequestException(
                '"' . $sCardExpYear . '" is an invalid expiry year; must be numeric.',
                1
            );
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Get the card's expiry year
     * @return string
     */
    public function getCardExpYear()
    {
        return $this->oCard->exp->year;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the card's CVC number
     *
     * @param string $sCardCvc The card's cvc number
     *
     * @return $this
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
     * @return string
     */
    public function getCardCvc()
    {
        return $this->oCard->cvc;
    }

    // --------------------------------------------------------------------------

    /**
     * Define a custom field
     *
     * @param string $sProperty The property to set
     * @param mixed  $mValue    The value to set
     *
     * @return $this
     */
    public function setCustomField($sProperty, $mValue)
    {
        $this->oCustomField->{$sProperty} = $mValue;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Retrieve a custom field
     *
     * @param  string $sProperty The property to retrieve
     *
     * @return mixed
     */
    public function getCustomField($sProperty)
    {
        return property_exists($this->oCustomField, $sProperty) ? $this->oCustomField->{$sProperty} : null;
    }

    // --------------------------------------------------------------------------

    /**
     * Set a custom value
     *
     * @param string $sProperty The property to set
     * @param mixed  $mValue    The value to set
     *
     * @return $this
     */
    public function setCustomData($sProperty, $mValue)
    {
        $this->oCustomData->{$sProperty} = $mValue;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Retrieve a custom value
     *
     * @param  string $sProperty The property to retrieve
     *
     * @return mixed
     */
    public function getCustomData($sProperty)
    {
        return property_exists($this->oCustomData, $sProperty) ? $this->oCustomData->{$sProperty} : null;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the description
     *
     * @param string $sDescription The description of the charge
     *
     * @return $this
     */
    public function setDescription($sDescription)
    {
        $this->sDescription = $sDescription;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the description
     * @return string
     */
    public function getDescription()
    {
        return $this->sDescription;
    }

    // --------------------------------------------------------------------------

    /**
     * Set whether the charge should automatically redirect
     *
     * @param boolean $bAutoRedirect Whether to auto redirect or not
     *
     * @return $this
     */
    public function setAutoRedirect($bAutoRedirect)
    {
        $this->bAutoRedirect = (bool) $bAutoRedirect;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Whether the charge request will automatically redirect in the case of a
     * driver requesting a redirect flow.
     * @return boolean
     */
    public function isAutoRedirect()
    {
        return $this->bAutoRedirect;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the URL to go to when a payment is completed
     *
     * @param string $sContinueUrl the URL to go to when payment is completed
     *
     * @return $this
     */
    public function setContinueUrl($sContinueUrl)
    {
        $this->sContinueUrl = $sContinueUrl;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the URL to go to when a payment is completed
     * @return string
     */
    public function getContinueUrl()
    {
        return $this->sContinueUrl;
    }

    // --------------------------------------------------------------------------

    /**
     * execute the charge
     *
     * @param  integer $iAmount   The amount to charge the card
     * @param  string  $sCurrency The currency in which to charge
     *
     * @throws ChargeRequestException
     * @return \Nails\Invoice\Model\ChargeResponse
     */
    public function execute($iAmount, $sCurrency)
    {
        //  Ensure we have a driver
        if (empty($this->oDriver)) {
            throw new ChargeRequestException('No driver selected.', 1);
        }

        //  Ensure we have an invoice
        if (empty($this->oInvoice)) {
            throw new ChargeRequestException('No invoice selected.', 1);
        }

        // --------------------------------------------------------------------------

        if (!is_int($iAmount) || $iAmount <= 0) {
            throw new ChargeRequestException('Amount must be a positive integer.', 1);
        }

        // --------------------------------------------------------------------------

        //  @todo: validate currency

        // --------------------------------------------------------------------------

        //  Create a charge against the invoice if one hasn't been specified
        if (empty($this->oPayment)) {

            $iPaymentId = $this->oPaymentModel->create(
                [
                    'driver'       => $this->oDriver->getSlug(),
                    'description'  => $this->getDescription(),
                    'invoice_id'   => $this->oInvoice->id,
                    'currency'     => $sCurrency,
                    'amount'       => $iAmount,
                    'url_continue' => $this->getContinueUrl(),
                    'custom_data'  => $this->oCustomData,
                ]
            );

            if (empty($iPaymentId)) {
                throw new ChargeRequestException('Failed to create new payment.', 1);
            }

            $this->setPayment($iPaymentId);
        }

        $mFields = $this->oDriver->getPaymentFields();

        if (!empty($mFields) && $mFields == 'CARD') {
            $oDriverData = $this->oCard;
        } else {
            $oDriverData = $this->oCustomField;
        }

        //  Return URL for drivers which implement a redirect flow
        $sSuccessUrl = site_url('invoice/payment/' . $this->oPayment->id . '/' . $this->oPayment->token . '/complete');
        $sFailUrl    = site_url('invoice/invoice/' . $this->oInvoice->ref . '/' . $this->oInvoice->token . '/pay');

        //  Execute the charge
        $oChargeResponse = $this->oDriver->charge(
            $iAmount,
            $sCurrency,
            $oDriverData,
            $this->oCustomData,
            $this->getDescription(),
            $this->oPayment,
            $this->oInvoice,
            $sSuccessUrl,
            $sFailUrl,
            $this->getContinueUrl()
        );

        //  Validate driver response
        if (empty($oChargeResponse)) {
            throw new ChargeRequestException('Response from driver was empty.', 1);
        }

        if (!($oChargeResponse instanceof ChargeResponse)) {
            throw new ChargeRequestException(
                'Response from driver must be an instance of \Nails\Invoice\Model\ChargeResponse.',
                1
            );
        }

        //  Handle the response
        if ($oChargeResponse->isRedirect() && $this->isAutoRedirect()) {

            /**
             * Driver uses a redirect flow, determine whether we can use a basic header redirect,
             * or if we need to POST some data to the endpoint
             */

            $sRedirectUrl = $oChargeResponse->getRedirectUrl();
            $aPostData    = $oChargeResponse->getRedirectPostData();

            if (empty($aPostData)) {

                redirect($sRedirectUrl);

            } else {

                $oCi = get_instance();
                echo $oCi->load->view('structure/header/blank', getControllerData(), true);
                echo $oCi->load->view(
                    'invoice/pay/post',
                    [
                        'redirectUrl' => $sRedirectUrl,
                        'postFields'  => $aPostData,
                    ],
                    true
                );
                echo $oCi->load->view('structure/footer/blank', getControllerData(), true);
                exit();
            }

        } elseif ($oChargeResponse->isProcessing()) {

            //  Driver has started processing the charge, but it hasn't been confirmed yet
            $this->setPaymentProcessing(
                $oChargeResponse->getTxnId(),
                $oChargeResponse->getFee()
            );

        } elseif ($oChargeResponse->isComplete()) {

            //  Driver has confirmed that payment has been taken.
            $this->setPaymentComplete(
                $oChargeResponse->getTxnId(),
                $oChargeResponse->getFee()
            );

        } elseif ($oChargeResponse->isFailed()) {

            //  Update the payment
            $sPaymentClass = get_class($this->oPaymentModel);
            $bResult       = $this->oPaymentModel->update(
                $this->oPayment->id,
                [
                    'status'    => $sPaymentClass::STATUS_FAILED,
                    'fail_msg'  => $oChargeResponse->getError()->msg,
                    'fail_code' => $oChargeResponse->getError()->code,
                ]
            );

            if (empty($bResult)) {
                throw new ChargeRequestException('Failed to update existing payment.', 1);
            }
        }

        //  Set the success and fail URLs
        $oChargeResponse->setSuccessUrl($sSuccessUrl);
        $oChargeResponse->setFailUrl($sFailUrl);
        $oChargeResponse->setContinueUrl($this->getContinueUrl());

        //  Lock the response so it cannot be altered
        $oChargeResponse->lock();

        return $oChargeResponse;
    }
}
