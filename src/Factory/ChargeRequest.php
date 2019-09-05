<?php

/**
 * Attempts a charge
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Factory
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Invoice\Factory;

use Nails\Factory;
use Nails\Invoice\Exception\ChargeRequestException;

class ChargeRequest extends RequestBase
{
    /**
     * The Card object
     *
     * @var \stdClass
     */
    protected $oCard;

    /**
     * The custom fields object
     *
     * @var \stdClass
     */
    protected $oCustomField;

    /**
     * The custom data object
     *
     * @var \stdClass
     */
    protected $oCustomData;

    /**
     * The charge description
     *
     * @var string
     */
    protected $sDescription = '';

    /**
     * Whether to honour automatic redirects or not
     *
     * @var bool
     */
    protected $bAutoRedirect = true;

    // --------------------------------------------------------------------------

    /**
     * ChargeRequest constructor.
     */
    public function __construct()
    {
        parent::__construct();

        //  Card details
        $this->oCard = (object) [
            'name'   => '',
            'number' => '',
            'exp'    => (object) [
                'month' => '',
                'year'  => '',
            ],
            'cvc'    => '',
        ];

        //  Container for custom fields and data
        $this->oCustomField = (object) [];
        $this->oCustomData  = (object) [];
    }

    // --------------------------------------------------------------------------

    /**
     * Set the cardholder's name
     *
     * @param string $sCardName The cardholder's name
     *
     * @return $this
     */
    public function setCardName(string $sCardName): ChargeRequest
    {
        $this->oCard->name = $sCardName;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the cardholder's Name
     *
     * @return string
     */
    public function getCardName(): string
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
    public function setCardNumber(string $sCardNumber): ChargeRequest
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
     *
     * @return string
     */
    public function getCardNumber(): string
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
    public function setCardExpMonth(string $sCardExpMonth): ChargeRequest
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
     *
     * @return string
     */
    public function getCardExpMonth(): string
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
    public function setCardExpYear(string $sCardExpYear): ChargeRequest
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
     *
     * @return string
     */
    public function getCardExpYear(): string
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
    public function setCardCvc(string $sCardCvc): ChargeRequest
    {
        //  Validate
        $this->oCard->cvc = $sCardCvc;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the card's CVC number
     *
     * @return string
     */
    public function getCardCvc(): string
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
    public function setCustomField(string $sProperty, $mValue): ChargeRequest
    {
        $this->oCustomField->{$sProperty} = $mValue;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Retrieve a custom field
     *
     * @param string $sProperty The property to retrieve
     *
     * @return mixed
     */
    public function getCustomField(string $sProperty)
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
    public function setCustomData(string $sProperty, $mValue): ChargeRequest
    {
        $this->oCustomData->{$sProperty} = $mValue;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Retrieve a custom value
     *
     * @param string $sProperty The property to retrieve
     *
     * @return mixed
     */
    public function getCustomData(string $sProperty)
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
    public function setDescription(string $sDescription): ChargeRequest
    {
        $this->sDescription = $sDescription;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the description
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->sDescription;
    }

    // --------------------------------------------------------------------------

    /**
     * Set whether the charge should automatically redirect
     *
     * @param bool $bAutoRedirect Whether to auto redirect or not
     *
     * @return $this
     */
    public function setAutoRedirect(bool $bAutoRedirect): ChargeRequest
    {
        $this->bAutoRedirect = (bool) $bAutoRedirect;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Whether the charge request will automatically redirect in the case of a
     * driver requesting a redirect flow.
     *
     * @return bool
     */
    public function isAutoRedirect(): bool
    {
        return $this->bAutoRedirect;
    }

    // --------------------------------------------------------------------------

    /**
     * xecute the charge
     *
     * @param int    $iAmount   The amount to charge the card
     * @param string $sCurrency The currency in which to charge
     *
     * @return ChargeResponse
     * @throws ChargeRequestException
     */
    public function execute(int $iAmount, string $sCurrency): ChargeResponse
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

        //  @todo (Pablo - 2019-09-04) - Validate currency is enabled

        // --------------------------------------------------------------------------

        //  Create a charge against the invoice if one hasn't been specified
        if (empty($this->oPayment)) {

            $iPaymentId = $this->oPaymentModel->create(
                [
                    'driver'      => $this->oDriver->getSlug(),
                    'description' => $this->getDescription(),
                    'invoice_id'  => $this->oInvoice->id,
                    'currency'    => $sCurrency,
                    'amount'      => $iAmount,
                    'url_success' => $this->getSuccessUrl(),
                    'url_error'   => $this->getErrorUrl(),
                    'custom_data' => $this->oCustomData,
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
        $sSuccessUrl = siteUrl('invoice/payment/' . $this->oPayment->id . '/' . $this->oPayment->token . '/complete');
        $sErrorUrl   = siteUrl('invoice/invoice/' . $this->oInvoice->ref . '/' . $this->oInvoice->token . '/pay');

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
            $sErrorUrl
        );

        //  Set the success and fail URLs
        $oChargeResponse->setSuccessUrl($sSuccessUrl);
        $oChargeResponse->setErrorUrl($sErrorUrl);

        //  Validate driver response
        if (empty($oChargeResponse)) {
            throw new ChargeRequestException('Response from driver was empty.', 1);
        }

        if (!($oChargeResponse instanceof ChargeResponse)) {
            throw new ChargeRequestException(
                'Response from driver must be an instance of \Nails\Invoice\Factory\ChargeResponse.',
                1
            );
        }

        //  Handle the response
        if ($oChargeResponse->isSca()) {

            /**
             * Payment requires SCA, redirect to handle this
             */

            $sScaData = json_encode($oChargeResponse->getScaData());
            $this->oPaymentModel->update(
                $this->oPayment->id,
                ['sca_data' => $sScaData]
            );

            $sRedirectUrl = siteUrl('invoice/payment/sca/' . $this->oPayment->token . '/' . md5($sScaData));
            if ($this->isAutoRedirect()) {
                redirect($sRedirectUrl);
            } else {
                $oChargeResponse->setScaUrl($sRedirectUrl);
                //  Set the redirect values too, in case dev has not considered SCA
                $oChargeResponse->setIsRedirect(true);
                $oChargeResponse->setRedirectUrl($sRedirectUrl);
            }

        } elseif ($oChargeResponse->isRedirect() && $this->isAutoRedirect()) {

            /**
             * Driver uses a redirect flow, determine whether we can use a basic header redirect,
             * or if we need to POST some data to the endpoint
             */

            $sRedirectUrl = $oChargeResponse->getRedirectUrl();
            $aPostData    = $oChargeResponse->getRedirectPostData();

            if (empty($aPostData)) {

                redirect($sRedirectUrl);

            } else {

                $oView = Factory::service('View');
                echo $oView->load('structure/header/blank', getControllerData(), true);
                echo $oView->load(
                    'invoice/pay/post',
                    [
                        'redirectUrl' => $sRedirectUrl,
                        'postFields'  => $aPostData,
                    ],
                    true
                );
                echo $oView->load('structure/footer/blank', getControllerData(), true);
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

            //  Driver reported a failure
            $this->setPaymentFailed(
                $oChargeResponse->getError()->msg,
                $oChargeResponse->getError()->code
            );
        }

        //  Lock the response so it cannot be altered
        $oChargeResponse->lock();

        return $oChargeResponse;
    }
}
